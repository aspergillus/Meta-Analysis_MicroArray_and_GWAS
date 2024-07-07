args <- commandArgs(TRUE)
vari <- args[1]
vari <- unlist(strsplit(vari, ","))
pVal <- as.numeric(args[2])
logU <- as.numeric(args[3])
logD <- as.numeric(args[4])

keep_cols_g <- c("Gene symbol", "Log2 fold change","P-value")
l = 1
gene_list <- c()
for(x in vari){
  setwd(paste0("C:/xampp/htdocs/gwas/microarray/fileUpload/",x))
  g <- read.table(file = "ModT-Results.txt")
  colnames(g) <- g[1,]
  g <- g[-1, ][,keep_cols_g]
  g$Study <- paste0("Study_",l)
  fi<-rbind(gene_list,g)
  gene_list <- fi
  l <- l+1
}

library(dplyr ,lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
gene_list <- filter(gene_list, gene_list$`Gene symbol` != "")
gene_list_dF = as.data.frame(gene_list)
gene_list_dF$`Log2 fold change` = as.numeric(as.character(gene_list_dF$`Log2 fold change`))

# use same table which displayed on site instead of below 3 lines
deg_list <- filter(gene_list_dF, gene_list_dF$`P-value` < pVal)
deg_list_u <- filter(deg_list, deg_list$`Log2 fold change` > logU)
deg_list_d <- filter(deg_list, deg_list$`Log2 fold change` < logD)
deg_list <- rbind(deg_list_u, deg_list_d)
colnames(deg_list)[1] <- "DEGs"

# HeatMap
library(reshape2, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
heatmap_data_meta_all <- dcast(deg_list, DEGs ~ Study, value.var="Log2 fold change")
colnames(heatmap_data_meta_all)[1] <- "genes"
heatmap_data_meta_all[is.na(heatmap_data_meta_all)] <- 0
write.table(heatmap_data_meta_all,"heat_meta_gene.txt",sep="\t",row.names=FALSE,quote=FALSE)

# Bubble plot
bubble_genes_all <- na.omit(deg_list)
Freq_genes_list <- as.data.frame(table(bubble_genes_all$DEGs))
common_btwn_deg <- filter(Freq_genes_list, Freq >= (l-1))  # select no. of studies common
colnames(common_btwn_deg) <- c("DEGs", "Freq")
common_btwn_deg <- merge(common_btwn_deg, deg_list, by="DEGs")
bubbleMetaData_DEGs <- common_btwn_deg
if (dim(bubbleMetaData_DEGs)[1] != 0) {
  for (i in 1:nrow(bubbleMetaData_DEGs)) {
    if(bubbleMetaData_DEGs$`Log2 fold change`[i] > 0){
      bubbleMetaData_DEGs$regulation[i] = "Upregulated"
    }else{
      bubbleMetaData_DEGs$regulation[i] = "Downregulated"
    }
  }
}
write.table(bubbleMetaData_DEGs, file="bubble_meta_gene.txt", sep="\t", row.names=FALSE, col.names=TRUE)
print("everything is working fine")
