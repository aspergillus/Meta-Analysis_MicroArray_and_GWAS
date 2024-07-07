args <- commandArgs(TRUE)
vari <- args[1]
vari_2 <- args[2]
vari_2 <- gsub('[%]', ' ', vari_2)
vari_3 <- strsplit(vari_2, ",")

# "C:/Program Files/R/R-4.0.5/bin/Rscript" "C:/wamp64/www/gwas/microArray/R_file/selBubbPlt_CEL.R" 9363 "HDL assembly, Glucuronidation, SARS-CoV-2 and COVID-19 Pathway, GSD IV"
# vari <- 223

setwd(paste0("C:/wamp64/www/gwas/microArray/fileUpload/",vari))
keep_cols_p <- c("Pathways", "X.P.value.","Normalized.enrichment.score..NES.","No..of.genes")
p1 <- read.csv(file = "gseaResult.csv")[,keep_cols_p]
names(p1)[2] <- "P.value"
names(p1)[3] <- "NES"

selected_pathways <- vari_3
selected_pathways <- as.data.frame(selected_pathways)
colnames(selected_pathways) <- "Pathways"
bubbleplot_selected_pathways <- merge(selected_pathways, p1, by="Pathways")

library(ggplot2 ,lib.loc = c("C:/Users/BIC_Dell_WS2/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
png("bubble_plot.png", family= "Calibri",width = 14, height = 10, units="in", res = 1080)
bubbleplot_selected_pathways$Pathways = with(bubbleplot_selected_pathways, reorder(Pathways,P.value)) # Change study number here
ggplot(bubbleplot_selected_pathways, aes(x = P.value, y = Pathways)) +
  geom_point(aes(color = NES, size = No..of.genes), alpha = 3.5) +
  #  scale_color_manual(values = c("#00AFBB", "#E7B800", "#FC4E07")) +
  scale_color_gradient2(low="red",mid = "White", midpoint = 0, high="green") +
  scale_size(range = c(1, 10))+  # Adjust the range of points size
  theme(axis.text.y = element_text(color = "black", size = 12,  face = "plain"))
dev.off()
print("Everything is working perfectly")