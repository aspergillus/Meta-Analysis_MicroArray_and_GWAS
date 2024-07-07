args <- commandArgs(TRUE)
vari <- args[1]
# vari_2 <- as.numeric(args[2])

# print(vari)
# print(vari_2)
# 
# vari = c("rs147859257,rs141853578,rs121913059,rs35292876,rs2230199,rs429358")
# vari_2 = 5

library(haploR, lib.loc = c("C:/Users/aman/Documents/R/win-library/4.0","C:/Program Files/R/R-4.0.5/library"))
setwd("C:/wamp64/www/gwas/microArray/R_file/Output")
b = queryHaploreg(query = vari, timeout = 1000)
write.table(b, file="haplo_output.txt", sep="\t", row.names=FALSE, col.names=TRUE)
print("Working fine!!!!!!!!!")