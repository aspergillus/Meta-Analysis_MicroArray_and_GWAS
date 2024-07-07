args <- commandArgs(TRUE)
vari <- args[1]
vari <- unlist(strsplit(vari, ","))

# vari = c("7425", "696", "7")
  
keep_cols_p <- c("Pathways", "P.value","Normalized.enrichment.score..NES.","No..of.genes", "Regulation.status")
l = 1
path_list <- c()
for(x in vari){
  setwd(paste0("C:/xampp/htdocs/gwas/microarray/fileUpload/",x))
  g <- unique(read.csv(file = "gseaResult.csv")[,keep_cols_p])
  if(dim(g)[1] != 0){
    names(g)[3] <- "NES"
    g <- g[order(g$Pathways, -g$NES),]
    g <- g[!duplicated(g$Pathways),]
    g$Study <- paste0("Study_",l)
    fi<-rbind(path_list,g)
    path_list <- fi 
  }
  l <- l+1
}

# HeatMap GSEA
library(reshape2, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
data_meta_all_path <- dcast(path_list, Pathways ~ Study, value.var="NES") 
heatmap_data_meta_all_path <- data_meta_all_path 
heatmap_data_meta_all_path[is.na(heatmap_data_meta_all_path)] <- 0
write.table(heatmap_data_meta_all_path,"heat_meta_pathway.txt",sep="\t",row.names=FALSE,quote=FALSE)

# Bubble plot GSEA
Freq_pathway_list <- as.data.frame(table(path_list$Pathways))
library(dplyr, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
common_mttwo <- filter(Freq_pathway_list, Freq >= (l-1))  # select no.of studies common
colnames(common_mttwo) <- c("Pathways", "Freq")
common_mttwo <- merge(common_mttwo, path_list, by="Pathways")
bubbleMetaData <- common_mttwo
write.table(bubbleMetaData, file="bubble_meta_pathway.txt", sep="\t", row.names=FALSE, col.names=TRUE)
print("Everything is working")
