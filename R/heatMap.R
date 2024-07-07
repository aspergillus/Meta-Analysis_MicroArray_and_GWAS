args <- commandArgs(TRUE)
vari <- args[1]
vari_2 <- args[2]
print(vari)
print(vari_2)

vari = 3892
setwd(paste0("C:/wamp64/www/gwas/microArray/fileUpload/",vari))

Expression_data_PCA <- read.delim("Expression_All_Normalized_GSE98595.txt")
data_PCA <- Expression_data_PCA[,2:ncol(Expression_data_PCA)] # removed 1st column
library(dplyr)
data_PCA <- filter(data_PCA, geneid != "")
heatmap_expre <- data_PCA[,]

# Same as for PCA
# Metadata file - user defined groups were used for metadata file
metadata_file <- read.table("metadata_GSE98595.txt") # create based on user uploaded files
metadata_file = metadata_file[-1,]
rownames(metadata_file) <- metadata_file$V1
metadata_file <- metadata_file[-1]

selected_genes <- c("DDR1", "RFC2", "PAX8", "MMP14", "F11R") # User selected genes
selected_genes <- as.data.frame(selected_genes)
colnames(selected_genes) <- "geneid"
heatmap_selected_genes <- merge(selected_genes, heatmap_expre, by="geneid")
rownames(heatmap_selected_genes) <- heatmap_selected_genes$geneid
heatmap_selected_genes <- heatmap_selected_genes[,2:ncol(heatmap_selected_genes)]

# install.packages("heatmaply")
library(heatmaply)
gradient_col <- scale_fill_gradient(low = "lightyellow", high = "darkgreen")
heatMap_Vari <- heatmaply(heatmap_selected_genes, scale = "none", na.value = "black",
           scale_fill_gradient_fun = gradient_col, cellnote = format(heatmap_selected_genes, digits=3),
           cellnote_size = 9, cellnote_color = "white",
           fontsize_row = 10, fontsize_col = 10,
           col_side_colors=metadata_file$V2, plot_method = "plotly",
           colorscale='Viridis',row_side_palette= byPal, dendrogram = "row",
           xlab = "Samples", ylab = "Genes")
export(heatMap_Vari, file = "heatMap.png")

library(htmlwidgets)
saveWidget(p, file=paste0( getwd(), "/HtmlWidget/plotlyHeatmap2.html"))

# "C:/Program Files/R/R-4.0.5/bin/Rscript" "C:/wamp64/www/gwas/microArray/fileUpload/3892/heatMap.R" '."$rand_name $tPvalue $tLogfcu $tLogfcd"
