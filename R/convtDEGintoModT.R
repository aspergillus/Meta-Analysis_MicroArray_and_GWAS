#!/usr/bin/env Rscript

args <- commandArgs(TRUE)
vari <- args[1]
setwd(paste0("C:/wamp64/www/gwas/microArray/fileUpload/",vari))
fileTken <- read.delim("degList.txt")
colnames(fileTken)[1] <- "Gene symbol"
colnames(fileTken)[2] <- "Log2 fold change"
colnames(fileTken)[3] <- "P-value"
write.table(fileTken, paste0("ModT-Results.txt"), sep="\t", row.names=FALSE, col.names=TRUE)
