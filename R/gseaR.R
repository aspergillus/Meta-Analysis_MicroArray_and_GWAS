#!/usr/bin/env Rscript

args <- commandArgs(TRUE)
vari <- args[1]

# vari = 2

library(tibble, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(fgsea, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(GSA, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
library(dplyr, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))

setwd(paste0("C:/xampp/htdocs/gwas/microarray/fileUpload/",vari))
ranks <- read.table(file = "Significant_DEGs.rnk")
ranks <- deframe(ranks)

setwd("C:/xampp/htdocs/gwas/microarray")
pathways.hallmark <- gmtPathways("Human_AllPathways_March_01_2021_symbol.gmt")
pathways.hs <- GSA.read.gmt("Human_AllPathways_March_01_2021_symbol.gmt")
p_hs <- as.data.frame(cbind(pathways.hs$geneset.names,pathways.hs$geneset.descriptions))
colnames(p_hs) <- c("pathway","pathways")

#Run the fgsea algorithm
fgseaRes <- fgsea(pathways=pathways.hallmark, stats=ranks, minSize = 2, maxSize =500, eps = 0.0)
#fgseaRes <- fgsea(pathways.hallmark, ranks, maxSize =500, eps = 0.0)
fgseaRes <- merge(fgseaRes, p_hs, by="pathway")
fgseaRes <- fgseaRes[,c("pathways","pathway", "pval", "padj", "ES", "NES", "size", "leadingEdge")]

#Order by normalized enrichment score (NES)
fgseaResTidy <- fgseaRes[order(fgseaRes$NES, decreasing = TRUE)]
fgseaResTidy <- filter(fgseaResTidy, fgseaResTidy$pathways !="untitled" & fgseaResTidy$pathways !="") 

# selected by p-value cutoff
fgseaResTidy <- filter(fgseaResTidy, fgseaResTidy$pval < 0.05)
fgseaResTidy$regulation <- ifelse(fgseaResTidy$NES >0, "Upregulated","Downregulated")
fgseaResTidy$Database <- sapply(strsplit(as.character(fgseaResTidy$pathway),'%'), "[", 2)
fgseaResTidy$DatabaseID <- sapply(strsplit(as.character(fgseaResTidy$pathway),'%'), "[", 3)

fgseaResTidy$leadingEdge<- as.character(fgseaResTidy$leadingEdge)
fgseaResTidy$leadingEdge<-gsub("[c()]", "", fgseaResTidy$leadingEdge)
fgseaResTidy$leadingEdge<-gsub('"', "", fgseaResTidy$leadingEdge)
fgseaResTidy$size <- lengths(strsplit(fgseaResTidy$leadingEdge, ", "))
fgseaResTidy$pathways<-gsub('<i>', "", fgseaResTidy$pathways)
fgseaResTidy$pathways<-gsub('< i>', "", fgseaResTidy$pathways)
fgseaResTidy <- fgseaResTidy[,c("pathways", "Database", "DatabaseID", "pval", "padj", "NES", "size", "leadingEdge", "regulation")]
colnames(fgseaResTidy)<- c("Pathways","Database", "Database ID","P-value","Adjusted P-value / Q-value", "Normalized enrichment score (NES)", "No. of genes", "Genes", "Regulation status")
setwd(paste0("C:/xampp/htdocs/gwas/microarray/fileUpload/",vari))
write.csv(fgseaResTidy, file = "gseaResult.csv", row.names=FALSE)

# Lollipop plot
data_table  <- fgseaResTidy

library(ggplot2)
# Create data
data <- data.frame(path=data_table$Pathways, nes=data_table$`Normalized enrichment score (NES)`, Regulation=data_table$`Regulation status`)
data <- data[order(data$path, -data$nes),]
data <- data[!duplicated(data$path),]
data <- data[order(-data$nes),]
UpR <- filter(data, data$Regulation =="Upregulated")
DwR <- filter(data, data$Regulation =="Downregulated")
plot_data <- rbind(head(UpR,10),tail(DwR,10))
write.table(plot_data, file = "lollipop_Plot_data.txt", sep = "\t", row.names = FALSE)

