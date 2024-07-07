#---- set working directory
setwd("F:/Ulka/BioMitra/MicroarrayDataAnalysis")

# save and load data
# save.image(file = "plots_cel_microarray_data.RData")
# load(file = "plots_cel_microarray_data.RData")

#----Plots
#--- I. PCA analysis -----

# Take normalized expression data to a matrix
#data_PCA <- compa2
Expression_data_PCA <- read.delim("Expression_All_Normalized_GSE98595.txt")
data_PCA <- Expression_data_PCA[,2:ncol(Expression_data_PCA)] # removed 1st column
all_comp <- na.omit(data_PCA) # keep completed data only
rownames(all_comp) <- all_comp$geneid
all_comp <- all_comp[-1]

# Metadata file - user defined groups were used for metadata file
metadata_file<- read.table("metadata_GSE98595.txt") # create based on user uploaded files
rownames(metadata_file) <- metadata_file$V1
metadata_file <- metadata_file[-1]

# Check
colnames(all_comp)==rownames(metadata_file)

# Run PCA
library(PCAtools)
pca_mod <- pca(mat=all_comp, metadata = metadata_file, scale = FALSE) # keep it same

# Data to access
# Loading data
loadings_pca <- pca_mod$loadings
# Sample data 
samples_pca <- pca_mod$rotated

# Plots - scree, biplot, samples and loadings plot

# Determine optimum number of PCs to retain
#a. perform Horn's parallel analysis
horn <- parallelPCA(all_comp)
components_select_horn <- horn$n
#b. elbow method
elbow <- findElbowPoint(pca_mod$variance)
components_select_elbow <- elbow

#1. Scree plot
library(ggplot2)
library(scales)
#png("scree_plot.png", family= "Calibri", width = 4, height = 3, units="in", res = 600, pointsize = 3)
screeplot(pca_mod,
          components = getComponents(pca_mod, ),
          vline = c(horn$n, elbow), title = "Scree plot", axisLabSize = 12, titleLabSize = 16,
          subtitleLabSize = 12,captionLabSize = 12) +
  geom_label(aes(x = horn$n + 0.3, y = 50,
                 label = 'Horn\'s', vjust = -1, size = 6)) +
  geom_label(aes(x = elbow + 0.3, y = 50,
                 label = 'Elbow method', vjust = -1, size = 6))  
#dev.off()

# #2. biplot
# biplot(pca_mod, showLoadings = TRUE, lab = metadata_file$V2, ellipse = TRUE)
# 
# #3. Pairs plot
# pairsplot(pca_mod, lab = metadata_file$V2)

#3. Scores plot
library(plotly)
PC1_var <- pca_mod$variance

plot_ly(x=samples_pca$PC1, y=samples_pca$PC2, z=samples_pca$PC3, 
        type="scatter3d", mode="markers", color=metadata_file$V2) %>% layout(
  scene = list(
    xaxis = list(title = paste("PC1 (",format((PC1_var[1]), digits = 3),"% variation)")),
    yaxis = list(title = paste("PC2 (",format((PC1_var[2]), digits = 3),"% variation)")),
    zaxis = list(title = paste("PC3 (",format((PC1_var[3]), digits = 3),"% variation)"))
  ))

# library(car)
# library(rgl)
# scatter3d(x=samples_pca$PC1, y=samples_pca$PC2, z=samples_pca$PC3, groups = factor(metadata_file$V2),
#           surface=FALSE, grid = FALSE, ellipsoid = TRUE)


#4. Loadings plot
plotloadings(pca_mod, rangeRetain = 0.05, labSize = 3, legendIconSize = 1, legendPosition = 'top', 
             col = c("gold", "white", "royalblue"),borderWidth = 0.8,
             colMidpoint = 0, shape = 21, shapeSizeRange = c(10, 10))


#--- II. Bubble plot for pathway -----
keep_cols_p <- c("Pathways", "P.value","NES","No..of.genes")
p1 <- read.csv(file = "gseaResult_1.csv")[,keep_cols_p]
p2 <- read.csv(file = "gseaResult_2.csv")[,keep_cols_p]
p3 <- read.csv(file = "gseaResult_3.csv")[,keep_cols_p]

# # 1. Bubble plot for individual dataset
# library(ggplot2)
# png("bubble_plot_individual_dataset_p1.png", family= "Calibri",width = 8, height = 20, units="in", res = 600)
# p1$Pathways = with(p1, reorder(Pathways,P.value)) # Change study number here
# ggplot(p1, aes(x = P.value, y = Pathways)) +
#   geom_point(aes(color = NES, size = No..of.genes), alpha = 3.5) +
#   #  scale_color_manual(values = c("#00AFBB", "#E7B800", "#FC4E07")) +
#   scale_color_gradient2(low="red",mid = "White", midpoint = 0, high="green") +
#   scale_size(range = c(0.2, 1))+  # Adjust the range of points size
#   theme(axis.text.y = element_text(color = "black", size = 5,  face = "plain"))
# dev.off()

# 2. For selected pathways
selected_pathways <- c("HDL assembly", "Glucuronidation", "SARS-CoV-2 and COVID-19 Pathway", "GSD IV") # User selected pathways
selected_pathways <- as.data.frame(selected_pathways)
colnames(selected_pathways) <- "Pathways"
bubbleplot_selected_pathways <- merge(selected_pathways, p1, by="Pathways")

# Bubble plot - selected pathways
# for download
library(ggplot2)
png("bubble_plot_selected_pathways_p1.png", family= "Calibri",width = 8, height = 10, units="in", res = 600)
#reorder pathways based on pvalue
bubbleplot_selected_pathways$Pathways = with(bubbleplot_selected_pathways, reorder(Pathways,P.value)) # Change study number here 
# create bubble plot
ggplot(bubbleplot_selected_pathways, aes(x = P.value, y = Pathways)) +
  geom_point(aes(color = NES, size = No..of.genes), alpha = 3.5) +
  #  scale_color_manual(values = c("#00AFBB", "#E7B800", "#FC4E07")) +
  scale_color_gradient2(low="red",mid = "White", midpoint = 0, high="green") +
  scale_size(range = c(1, 10))+  # Adjust the range of points size
  theme(axis.text.y = element_text(color = "black", size = 12,  face = "plain"))
dev.off()

# for interactive plot
library(ggplot2)
library(plotly)
#reorder pathways based on pvalue
bubbleplot_selected_pathways$Pathways = with(bubbleplot_selected_pathways, reorder(Pathways,P.value)) # Change study number here 
# create bubble plot
bubble_plt_selct_path <- ggplot(bubbleplot_selected_pathways, aes(x = P.value, y = Pathways)) +
  geom_point(aes(color = NES, size = No..of.genes), alpha = 3.5) +
  #  scale_color_manual(values = c("#00AFBB", "#E7B800", "#FC4E07")) +
  scale_color_gradient2(low="red",mid = "White", midpoint = 0, high="green") +
  scale_size(range = c(1, 10))+  # Adjust the range of points size
  theme(axis.text.y = element_text(color = "black", size = 12,  face = "plain"))
ggplotly(bubble_plt_selct_path)


# Add study name to column
p1$Study <- "Study 1"
p2$Study <- "Study 2"
p3$Study <- "Study 3"

# 3.Collate all pathways from different studies
pathway_list <- as.data.frame(rbind(as.data.frame(p1), as.data.frame(p2), as.data.frame(p3)))

# # Bubble plot for all collated pathways
# library(ggplot2)
# png("bubble_plot_metaanalysis_3datasets.png", family= "Calibri",width = 8, height = 20, units="in", res = 600)
# pathway_list$Pathways = with(pathway_list, reorder(Pathways, No..of.genes))
# ggplot(pathway_list[c(1:100,280:380,458:500),], aes(x = Study, y = Pathways)) +
#   geom_point(aes(color = NES, size = No..of.genes), alpha = 3.5) +
# #  scale_color_manual(values = c("#00AFBB", "#E7B800", "#FC4E07")) +
#   scale_color_gradient2(low="red",mid = "White", midpoint = 0, high="green") +
#   scale_size(range = c(0.2, 1))+  # Adjust the range of points size
#   theme(axis.text.y = element_text(color = "black", size = 5,  face = "plain"))
# dev.off()

# For common pathways
# Common_pathways <- as.data.frame(Reduce(intersect, list(p1$Pathways,p2$Pathways,p3$Pathways)))
# colnames(Common_pathways)[1] <- "Pathways"

# 4. For pathways common in two or more studies
Freq_pathway_list <- as.data.frame(table(pathway_list$Pathways))
library(dplyr)
common_mttwo <- filter(Freq_pathway_list, Freq >=2)  # select no.of studies common
colnames(common_mttwo)[1] <- "Pathways"
common_mttwo <- merge(common_mttwo, pathway_list, by="Pathways")

# for download plot
png("bubble_plot_metaanalysis_morethan2datasets.png", family= "Calibri",width = 8, height = 10, units="in", res = 600)
common_mttwo$Pathways = with(common_mttwo, reorder(Pathways, No..of.genes))
ggplot(common_mttwo, aes(x = Study, y = Pathways)) +
  geom_point(aes(color = NES, size = No..of.genes), alpha = 1) +
  #  scale_color_manual(values = c("#00AFBB", "#E7B800", "#FC4E07")) +
  scale_color_gradient2(low="red",mid = "White", midpoint = 0, high="green") +
  scale_size(range = c(0.5,5))+  # Adjust the range of points size
  theme(axis.text.y = element_text(color = "black", size = 7,  face = "plain"))
dev.off()

# for interactive plot
library(plotly)
common_mttwo$Pathways = with(common_mttwo, reorder(Pathways, No..of.genes))
bubble_plt_mttwo <- ggplot(common_mttwo, aes(x = Study, y = Pathways)) +
  geom_point(aes(color = NES, size = No..of.genes), alpha = 1) +
  #  scale_color_manual(values = c("#00AFBB", "#E7B800", "#FC4E07")) +
  scale_color_gradient2(low="red",mid = "White", midpoint = 0, high="green") +
  scale_size(range = c(0.5,5))+  # Adjust the range of points size
  theme(axis.text.y = element_text(color = "black", size = 7,  face = "plain"))
ggplotly(bubble_plt_mttwo)

#--- III. Heatmaps ---- 
#---- 1. For Expreseeion data ----
# Read data - Expression data
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

# #install.packages("heatmaply")
library(heatmaply)
gradient_col <- scale_fill_gradient(low = "lightyellow", high = "darkgreen")
heatmaply(heatmap_selected_genes, scale = "none", na.value = "black",
          scale_fill_gradient_fun = gradient_col, 
          fontsize_row = 10, fontsize_col = 10, 
          col_side_colors=metadata_file$V2, plot_method = "plotly",
          colorscale='Viridis',row_side_palette= byPal, dendrogram = "row",
          xlab = "Samples", ylab = "Genes")


#---- 2. For Analyzed data ----
# Read data - analysis result
keep_cols_g <- c("names", "logFC","P.Value")
g1 <- read.table(file = "ModT-Results-2-vs-1_GSE98421.txt", header = TRUE)[,keep_cols_g]
g2 <- read.table(file = "ModT-Results-2-vs-1_GSE1615.txt", header = TRUE)[,keep_cols_g]
g3 <- read.table(file = "ModT-Results-2-vs-1_GSE98595.txt", header = TRUE)[,keep_cols_g]

g1$Study <- "Study 1"
g2$Study <- "Study 2"
g3$Study <- "Study 3"

# Collate all pathways from different studies
gene_list <- as.data.frame(rbind(as.data.frame(g1), as.data.frame(g2), as.data.frame(g3)))
# gene_list[gene_list == " "] <- ""
gene_list <- filter(gene_list, names != "")

# Keep DEGs
library(dplyr)
# use same table which displayed on site instead of below 3 lines
deg_list <- filter(gene_list, P.Value < 0.05)
deg_list <- filter(gene_list, abs(logFC) > 1)
colnames(deg_list)[1] <- "DEGs"

library(reshape2)
heatmap_data_meta_all <- dcast(deg_list, DEGs ~ Study, value.var="logFC")
rownames(heatmap_data_meta_all) <- heatmap_data_meta_all$DEGs
heatmap_data_meta_all <- heatmap_data_meta_all[,2:ncol(heatmap_data_meta_all)]

# Heat-map for meta-analysis - all DEGs
# if (!requireNamespace("BiocManager", quietly = TRUE))
#   install.packages("BiocManager")
# BiocManager::install("ComplexHeatmap")

# library(ComplexHeatmap)
# Heatmap(heatmap_data_meta_all, name = "Heatmap")
#
# Heatmap for individual studies
# #install.packages("heatmaply")
# library(heatmaply)
# gradient_col <- scale_fill_gradient2(low = "blue", high = "red", mid = "yellow", midpoint = 0, na.value = "grey50")
# heatmaply(heatmap_data_meta_all, scale = "none", na.value = "black",
#           scale_fill_gradient_fun = gradient_col, fontsize_row = 5,
#           Rowv = FALSE, Colv = FALSE)

# For pathways common in two or more studies
Freq_gene_list <- as.data.frame(table(deg_list$DEGs))

common_genes_mttwo <- filter(Freq_gene_list, Freq >=2)
colnames(common_genes_mttwo)[1] <- "DEGs"
common_genes_mttwo <- merge(common_genes_mttwo, deg_list, by="DEGs")

library(reshape2)
heatmap_data_meta_mttwo <- dcast(common_genes_mttwo, DEGs ~ Study, value.var="logFC")
rownames(heatmap_data_meta_mttwo) <- heatmap_data_meta_mttwo$DEGs
heatmap_data_meta_mttwo <- heatmap_data_meta_mttwo[,2:ncol(heatmap_data_meta_mttwo)]

# Heat-map for meta-analysis - two and more studies
# Heatmap(heatmap_data_meta_mttwo, row_names_gp = gpar(fontsize = 7))

# Heatmap for genes common in 2 and more than 2 studies
#install.packages("heatmaply")
library(heatmaply)
gradient_col <- scale_fill_gradient2(low = "blue", high = "red", mid = "white", midpoint = 0, na.value = "grey50")
heatmaply(heatmap_data_meta_mttwo, scale = "none", 
          scale_fill_gradient_fun = gradient_col, fontsize_row = 8,
          Rowv = FALSE, Colv = FALSE)




# Extra

#----Extra
#---- All plot in single panel----

# # Determine optimum number of PCs to retain
# #a. perform Horn's parallel analysis
# horn <- parallelPCA(all_comp)
# #b. elbow method
# elbow <- findElbowPoint(pca_mod$variance)
# 
# #1. Scree plot
# library(ggplot2)
# library(scales)
# scree_plot <- screeplot(pca_mod,
#           components = getComponents(pca_mod, 1:4),
#           vline = c(horn$n, elbow)) +
#   geom_label(aes(x = horn$n + 0.3, y = 50,
#                  label = 'Horn\'s', vjust = -1, size = 8)) +
#   geom_label(aes(x = elbow + 0.3, y = 50,
#                  label = 'Elbow method', vjust = -1, size = 8))  
# 
# #2. biplot
# bi_plot <- biplot(pca_mod, showLoadings = TRUE, lab = metadata_file$V2, ellipse = TRUE)
# 
# #3. Pairs plot
# pairs_plot <- pairsplot(pca_mod, lab = metadata_file$V2)
# 
# #4. Loadings plot
# loadings_plot <- plotloadings(pca_mod, rangeRetain = 0.05, labSize = 3, legendIconSize = 1, legendPosition = 'top', 
#              col = c("gold", "white", "royalblue"),borderWidth = 0.8,
#              colMidpoint = 0, shape = 21, shapeSizeRange = c(2, 10))
# 
# library(cowplot)
# library(ggplotify)
# 
# top_row <- plot_grid(scree_plot, pairs_plot,
#                      ncol = 2,
#                      labels = c('A', 'B  Pairs plot'),
#                      label_fontfamily = 'serif',
#                      label_fontface = 'bold',
#                      label_size = 22,
#                      align = 'h',
#                      rel_widths = c(1.10, 0.80))
# 
# bottom_row <- plot_grid(bi_plot, loadings_plot,
#                         ncol = 2,
#                         labels = c('C', 'D'),
#                         label_fontfamily = 'serif',
#                         label_fontface = 'bold',
#                         label_size = 22,
#                         align = 'h',
#                         rel_widths = c(1.2, 1.8))
# 
# plot_table <- plot_grid(top_row, bottom_row, ncol = 1,
#           rel_heights = c(2, 2))
# 
# plot_table





