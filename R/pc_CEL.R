args <- commandArgs(TRUE)
vari <- args[1]
vari_2 <- args[2]
vari_2 <- unlist(strsplit(vari_2, ","))

selPC_x <- vari_2[1]
selPC_y <- vari_2[2]

print(selPC_x)
print(selPC_y)
setwd(paste0("C:/xampp/htdocs/gwas/microarray/fileUpload/",vari))
samples_pca <- read.delim("samplesPCA.txt")
scoreplot_data <- samples_pca[,c(selPC_x,selPC_y,"Sample")]
colnames(scoreplot_data) <- c("PC_x", "PC_y","Sample")
metadata_file_ori<- read.table("group_file.txt", header=TRUE)
scoreplot_data <- merge(scoreplot_data,metadata_file_ori, by="Sample")
write.table(scoreplot_data, file="scoreplot_data_new.txt", sep="\t", row.names=FALSE, col.names=TRUE)
print("Everything is working")