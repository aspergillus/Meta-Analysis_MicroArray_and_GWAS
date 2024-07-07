#!/usr/bin/env Rscript

args <- commandArgs(TRUE)
vari <- args[1]
pval_cutoff <- as.numeric(args[2])
logfcu <- as.numeric(args[3])
logfcd <- as.numeric(args[4])

# vari = 3397
# pval_cutoff = 2
# logfcu = 2
# logfcd = -2

library(affy, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(limma, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(biomaRt)
library(RSQLite, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(DBI, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(dplyr, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(stringr, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))

setwd(paste0("C:/xampp/htdocs/gwas/fileUpload/",vari))

#a simple function for finding names later, keep the shortest name, usually the official name, for analysis
IllumMat<-function(x) {
  temp<-Symb3[which(Symb3[,2]==x[1]),3]
  fn<-temp[1]
  if (length(temp)>1){
    for (i in 1:length(temp)){
      if (nchar(fn)>nchar(temp[i])){
        fn<-temp[i]
      }
    }
  }
  return(fn)
}

NamMat<-function(x) {
  return(UseGenes[which(rownames(UseGenes)==x[1]),1])
}

#read sample attributes in Group.txt file
g1<-list.files(pattern = "group_file.txt")        #### Write group file name here##
group<-read.table(g1,header=TRUE,sep="\t")
gc1<-table(group[,2])

#read, rma background correction, quantile normalization, and pm correction
datamQN<- justRMA()
RawGenes<-exprs(datamQN)
RawGenes<-cbind(rownames(RawGenes),RawGenes)
colnames(RawGenes)[1]<-"ProbeID"

#Update gene symbols from BioMart using Illumina ID
mart = useMart("ENSEMBL_MART_ENSEMBL",dataset="hsapiens_gene_ensembl", host="www.ensembl.org")
Symb3 <- getBM(attributes = c('ensembl_gene_id', 'affy_hg_u133_plus_2', 'external_gene_name', 'hgnc_symbol', 'gene_biotype', 'description'), filters='affy_hg_u133_plus_2', values=RawGenes[,1], mart=mart)

ExpressOutDF = data.frame()
# check and add if loop for ST platform
{
  if (nrow(Symb3) != 0)
  {
    #Update Gene Symbols in UseGenes and keep ones with updated symbol
    Symb2<-apply(RawGenes,1,IllumMat)
    UseGenes<-cbind(Symb2,RawGenes)
    UseGenes<-UseGenes[!(is.na(UseGenes[,1])|(UseGenes[,1])==""),]
    UseGenes<-UseGenes[rowSums(is.na(UseGenes))<1,]
    
    
    #Collapse duplicated genes by selecting the probe with highest SD
    DupGenes<-unique(UseGenes[duplicated(UseGenes[,1]),1])
    for (i in 1:length(DupGenes)){
      dat<-NULL
      sdv<-NULL
      selected<-NULL
      indx<-NULL
      indx <- which(UseGenes[,1] == DupGenes[i])
      dat<-UseGenes[indx,]
      sdv<-apply(dat[,3:ncol(dat)], 1, sd)
      selected<-names(sdv)[which(sdv==max(sdv))]
      dat<-dat[which(rownames(dat)==selected[1]),]
      for (j in 1:length(indx)){
        UseGenes[indx[j],]<-dat 
        #replace all duplicated genes with values of max SD
      }}
    
    UseGenes <- UseGenes[!duplicated(UseGenes[,1]), ]
    UseGenes[,3:ncol(UseGenes)]<-as.numeric(UseGenes[,3:ncol(UseGenes)])
    group2<-group[order(group[,2]),]
    ExpressOut<-UseGenes[,c(1,1)]
    name3<-NULL
    for (i in 1:nrow(group2)){
      ExpressOut<-cbind(ExpressOut,UseGenes[,which(colnames(UseGenes)==group2[i,1])])
      name3<-c(name3,colnames(UseGenes)[which(colnames(UseGenes)==group2[i,1])])
    }
    
    colnames(ExpressOut)<-c("Name","geneid",as.character(name3))
    ExpressOutDF <- as.data.frame(trimws(ExpressOut))
    write.table(ExpressOutDF,"Expression_All_Normalized.txt",sep="\t",row.names=FALSE,quote=FALSE)
    
    med<-apply(ExpressOut[,3:ncol(ExpressOut)],1,function(x) median(x, na.rm = TRUE))
    ExpressOutMed<-matrix(as.numeric(ExpressOut[,3:ncol(ExpressOut)]),ncol=(ncol(ExpressOut)-2))-as.numeric(med)
    ExpressOutMed<-cbind(ExpressOut[,1:2],ExpressOutMed)
    colnames(ExpressOutMed)<-colnames(ExpressOut)
    write.table(ExpressOutMed,"MedianCentered_All.txt",sep="\t",row.names=FALSE,quote=FALSE)
    
    #Determine the number of comparisons, choose the groups, and process the data by limma
    exp1<-group[which(group[,2]==names(gc1)[2]),1]
    data1<-UseGenes[,which(colnames(UseGenes) %in% exp1)]
    con1<-group[which(group[,2]==names(gc1)[1]),1]
    data2<-UseGenes[,which(colnames(UseGenes) %in% con1)]
    
    #This is a simpler way to code ModT for two group comparison by generating design matrix with a contrast column (Exp vs Con) instead individual group label 
    design<-matrix(ncol=2,nrow=(gc1[2]+gc1[1]),data=1)
    rownames(design)<-c(as.character(exp1),as.character(con1))
    colnames(design)<-c("All","EvC")
    design[(length(exp1)+1):nrow(design),2]<-0
    compa<-cbind(data1,data2)
    compa2<-matrix(as.numeric(compa),ncol=ncol(compa))
    colnames(compa2)<-colnames(compa)
    rownames(compa2)<-rownames(compa)
    
    #ModT Test and output results, .rnk (T-values),Express, and cls files
    fit<-lmFit(compa2,design)
    fit<-eBayes(fit)
    result1<-topTable(fit,coef="EvC",adjust="BH",number=nrow(compa2))
    result1<-cbind(rownames(result1),result1)
    names<-apply(result1,1,NamMat)
    result1<<-cbind(names,result1)
    
    cols.dont.want <- "rownames(result1)"
    result1 <- result1[, ! names(result1) %in% cols.dont.want, drop = F]
    names(result1)[names(result1) == "names"] <- "Gene symbol"
    names(result1)[names(result1) == "logFC"] <- "Log2 fold change"
    names(result1)[names(result1) == "AveExpr"] <- "Average expression"
    names(result1)[names(result1) == "t"] <- "Moderated t-statistics (t)"
    names(result1)[names(result1) == "P.Value"] <- "P-value"
    names(result1)[names(result1) == "adj.P.Val"] <- "Adjusted p-Value / q-value"
    names(result1)[names(result1) == "B"] <- "B-statistics"
    result1$`Gene symbol` <- str_remove_all(result1$`Gene symbol`, " ")
    write.table(result1,file="ModT-Results.txt",sep="\t",row.names=FALSE,col.names=TRUE)
    
    signifcant_DEG1 <- filter(result1, (result1$`P-value` < pval_cutoff))
    signifcant_DEG2 <- filter(signifcant_DEG1, (signifcant_DEG1$`Log2 fold change` >= logfcu) | (signifcant_DEG1$`Log2 fold change` <= logfcd))
    signifcant_DEG <- signifcant_DEG2
    names(signifcant_DEG)[names(signifcant_DEG) == "Log2 fold change"] <- "Log<sub>2</sub> fold change"
    write.table(signifcant_DEG, file="Significant_DEGs.txt",sep="\t",row.names=FALSE,col.names=TRUE)
    
    rnkfile<-cbind(as.character(signifcant_DEG[,1]),as.numeric(signifcant_DEG[,5]))
    rnkfile<-rnkfile[!is.na(rnkfile[,2]),]
    write.table(rnkfile,file="Significant_DEGs.rnk",sep="\t",row.names=FALSE,col.names=FALSE,quote=FALSE)
    
    compa2<-cbind(UseGenes[,1],UseGenes[,1],compa2)
    colnames(compa2)[1:2]<-c("Name","geneid")
    write.table(compa2,paste0("Expression_",names(gc1)[2],"_vs_",names(gc1)[1],".txt"),sep="\t",row.names=FALSE,col.names=TRUE,quote=FALSE)
    
    clsfile<-c(rep(names(gc1)[2],gc1[2]),rep(names(gc1[1]),gc1[1]))
    fileConn<-file(paste0(names(gc1)[2],"_vs_",names(gc1)[1],"_classes.cls"))
    writeLines(c(paste(length(clsfile),"2 1"),paste("# ", unique(clsfile)[1], " ",unique(clsfile)[2])), fileConn)
    write.table(t(clsfile),paste(names(gc1)[2],"_vs_",names(gc1)[1],"_classes.cls",sep=""),col.name=FALSE,sep="\t",row.names=FALSE,quote=FALSE,append=TRUE)
    
    # print("Everything is working fine")
  }
  else
  {
    library(oligo, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
    library(limma, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
    
    #read, rma background correction, quantile normalization, and pm correction
    celfiles <- list.files(pattern = "CEL", full = TRUE)
    dataRaw<- read.celfiles(celfiles)
    datamQN<- rma(dataRaw)
    featureData(datamQN) <- getNetAffx(datamQN, 'transcript')
    RawGenes<-exprs(datamQN)
    RefSeq<-apply(datamQN@featureData@data,1,function(x) unlist(strsplit(x[8],"//"))[1])
    Symb1<-apply(datamQN@featureData@data,1,function(x) unlist(strsplit(x[8],"//"))[2])
    RawGenes<-cbind(Symb1,RefSeq,RawGenes)
    colnames(RawGenes)[1:2]<-c("GeneName","RefSeq")
    UseGenes<-RawGenes
    UseGenes<-UseGenes[!is.na(UseGenes[,1]),]
    
    #Collapse duplicated genes by selecting the probe with highest SD
    DupGenes<-unique(UseGenes[duplicated(UseGenes[,1]),1])
    for (i in 1:length(DupGenes)){
      dat<-NULL
      sdv<-NULL
      selected<-NULL
      indx<-NULL
      indx <- which(UseGenes[,1] == DupGenes[i])
      dat<-UseGenes[indx,]
      sdv<-apply(dat[,3:ncol(dat)], 1, sd)
      selected<-names(sdv)[which(sdv==max(sdv))]
      dat<-dat[which(rownames(dat)==selected)[1],]
      for (j in 1:length(indx)){
        UseGenes[indx[j],]<-dat #replace all duplicated genes with values of max SD
      }
    }
    
    UseGenes <- UseGenes[!duplicated(UseGenes[,1]), ]
    UseGenes[,3:ncol(UseGenes)]<-as.numeric(UseGenes[,3:ncol(UseGenes)])
    group2<-group[order(group[,2]),]
    ExpressOut<-UseGenes[,c(1,1)]
    name3<-NULL
    for (i in 1:nrow(group2)){
      ExpressOut<-cbind(ExpressOut,UseGenes[,which(colnames(UseGenes)==group2[i,1])])
      name3<-c(name3,colnames(UseGenes)[which(colnames(UseGenes)==group2[i,1])])
    }
    colnames(ExpressOut)<-c("Name","geneid",as.character(name3))
    ExpressOutDF <- as.data.frame(trimws(ExpressOut))
    write.table(ExpressOutDF,"Expression_All_Normalized.txt",sep="\t",row.names=FALSE,quote=FALSE)
    
    med<-apply(ExpressOut[,3:ncol(ExpressOut)],1,median)
    ExpressOutMed<-matrix(as.numeric(ExpressOut[,3:ncol(ExpressOut)]),ncol=(ncol(ExpressOut)-2))-as.numeric(med)
    ExpressOutMed<-cbind(ExpressOut[,1:2],ExpressOutMed)
    colnames(ExpressOutMed)<-colnames(ExpressOut)
    write.table(ExpressOutMed,"MedianCentered_All.txt",sep="\t",row.names=FALSE,quote=FALSE)
    
    #Determine the number of comparisons, choose the groups, and process the data by limma
    exp1<-group[which(group[,2]==names(gc1)[2]),1]
    data1<-UseGenes[,which(colnames(UseGenes) %in% exp1)]
    con1<-group[which(group[,2]==names(gc1)[1]),1]
    data2<-UseGenes[,which(colnames(UseGenes) %in% con1)]
    
    #This is a simpler way to code ModT for two group comparison by generating design matrix with a contrast column (Exp vs Con) instead individual group label 
    design<-matrix(ncol=2,nrow=(gc1[2]+gc1[1]),data=1)
    rownames(design)<-c(as.character(exp1),as.character(con1))
    colnames(design)<-c("All","EvC")
    design[(length(exp1)+1):nrow(design),2]<-0
    compa<-cbind(data1,data2)
    compa2<-matrix(as.numeric(compa),ncol=ncol(compa))
    colnames(compa2)<-colnames(compa)
    rownames(compa2)<-rownames(compa)
    
    #ModT Test and output results, .rnk (T-values),Express, and cls files
    fit<-lmFit(compa2,design)
    fit<-eBayes(fit)
    result1<-topTable(fit,coef="EvC",adjust="BH",number=nrow(compa2))
    result1<-cbind(rownames(result1),result1)
    names<-apply(result1,1,NamMat)
    result1<-cbind(names,result1)
     
    cols.dont.want <- "rownames(result1)"
    result1 <- result1[, ! names(result1) %in% cols.dont.want, drop = F]
    names(result1)[names(result1) == "names"] <- "Gene symbol"
    names(result1)[names(result1) == "logFC"] <- "Log2 fold change"
    names(result1)[names(result1) == "AveExpr"] <- "Average expression"
    names(result1)[names(result1) == "t"] <- "Moderated t-statistics (t)"
    names(result1)[names(result1) == "P.Value"] <- "P-value"
    names(result1)[names(result1) == "adj.P.Val"] <- "Adjusted p-Value / q-value"
    names(result1)[names(result1) == "B"] <- "B-statistics"
    result1$`Gene symbol` <- str_remove_all(result1$`Gene symbol`, " ")
    write.table(result1,file="ModT-Results.txt",sep="\t",row.names=FALSE,col.names=TRUE)
    
    signifcant_DEG1 <- filter(result1, (result1$`P-value` < pval_cutoff))
    signifcant_DEG2 <- filter(signifcant_DEG1, (signifcant_DEG1$`Log2 fold change` >= logfcu) | (signifcant_DEG1$`Log2 fold change` <= logfcd))
    signifcant_DEG <- signifcant_DEG2
    names(signifcant_DEG)[names(signifcant_DEG) == "Log2 fold change"] <- "Log<sub>2</sub> fold change"
    write.table(signifcant_DEG, file="Significant_DEGs.txt",sep="\t",row.names=FALSE,col.names=TRUE)
    
    rnkfile<-cbind(as.character(signifcant_DEG[,1]),as.numeric(signifcant_DEG[,5]))
    rnkfile<-rnkfile[!is.na(rnkfile[,2]),]
    write.table(rnkfile,file="Significant_DEGs.rnk",sep="\t",row.names=FALSE,col.names=FALSE,quote=FALSE)
    
    compa2<-cbind(UseGenes[,1],UseGenes[,1],compa2)
    colnames(compa2)[1:2]<-c("Name","geneid")
    write.table(compa2,paste0("Expression_",names(gc1)[2],"_vs_",names(gc1)[1],".txt"),sep="\t",row.names=FALSE,col.names=TRUE,quote=FALSE)
    
    clsfile<-c(rep(names(gc1)[2],gc1[2]),rep(names(gc1[1]),gc1[1]))
    fileConn<-file(paste0(names(gc1)[2],"_vs_",names(gc1)[1],"_classes.cls"))
    writeLines(c(paste(length(clsfile),"2 1"),paste("# ", unique(clsfile)[1], " ",unique(clsfile)[2])), fileConn)
    write.table(t(clsfile),paste(names(gc1)[2],"_vs_",names(gc1)[1],"_classes.cls",sep=""),col.name=FALSE,sep="\t",row.names=FALSE,quote=FALSE,append=TRUE)
    
    # print("Everything is working fine")
  }
}

# All Plot(Scree, Scores and Loading Plot)
Expression_data_PCA <- ExpressOutDF
data_PCA <- Expression_data_PCA[,2:ncol(Expression_data_PCA)] # removed 1st column
all_comp <- na.omit(data_PCA) # keep completed data only
rownames(all_comp) <- all_comp$geneid
all_comp <- all_comp[-1]
all_comp <- mutate_all(all_comp, function(x) as.numeric(as.character(x)))

# Metadata file - user defined groups were used for metadata file
metadata_file <- group # create based on user uploaded files
colnames(metadata_file) <- c("V1", "V2")
rownames(metadata_file) <- metadata_file$V1
metadata_file <- metadata_file[-1]

# Run PCA
library(PCAtools ,lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
pca_mod <- pca(mat=all_comp, metadata = metadata_file, scale = FALSE) # keep it same

############## Data to access ############
# Loading data
loadings_pca <- pca_mod$loadings
# Sample data 
samples_pca <- pca_mod$rotated
numPC <- colnames(samples_pca)
samples_pca$Sample <- rownames(samples_pca)
write.table(numPC, file = "numPC.txt", sep="\t", row.names=FALSE, col.names = TRUE)
write.table(samples_pca, file = "samplesPCA.txt", sep="\t", row.names=FALSE, col.names = TRUE)

#b. elbow method
elbow <- findElbowPoint(pca_mod$variance)
# components_select_elbow <- elbow
print(elbow)

#1. Scree plot
library(ggplot2 ,lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(scales ,lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))

variance <- as.data.frame(pca_mod$variance)
variance$PCs <- rownames(variance)
colnames(variance)[1] <- "variance"
variance$linered <- cumsum(variance$variance)
variance <- filter(variance, variance > 0.01)
write.table(variance, file="variance.txt", sep="\t", row.names=FALSE, col.names=TRUE)

#3. Loadings plot
load_pca_all <- plotloadings(pca_mod, rangeRetain = 0.01,
                             labSize = 5, legendIconSize = 5, legendPosition = 'top',
                             components = getComponents(pca_mod, seq_len(ncol(pca_mod$loadings))),
                             col = c("gold", "white", "royalblue"),borderWidth = 0.3,
                             colMidpoint = 0, shape = 21, shapeSizeRange = c(10, 10)) + 
  theme(axis.text.x = element_text(size = 20), axis.text.y = element_text(size = 20))
png("loading_plot.png", family= "Calibri", width = 10, height = 10, units="in", res=600)
load_pca_all
dev.off()

load_pca_all_table <- load_pca_all$data
write.table(load_pca_all_table, file = "Loadingplot_table_all_PCs.txt", sep = "\t", row.names = FALSE)

load_pca_sel <- plotloadings(pca_mod, rangeRetain = 0.01,
                             labSize = 5, legendIconSize = 5, legendPosition = 'top',
                             components = getComponents(pca_mod, seq_len(elbow)),
                             col = c("gold", "white", "royalblue"),borderWidth = 0.3,
                             colMidpoint = 0, shape = 21, shapeSizeRange = c(10, 10)) + 
  theme(axis.text.x = element_text(size = 20), axis.text.y = element_text(size = 20))
png("loading_plot_SelectedComp.png", family= "Calibri", width = 12, height = 10, units="in", res=600)
load_pca_sel
dev.off()

load_pca_sel_table <- load_pca_sel$data
write.table(load_pca_sel_table, file = "Loadingplot_table_selected_PCs.txt", sep = "\t", row.names = FALSE)
