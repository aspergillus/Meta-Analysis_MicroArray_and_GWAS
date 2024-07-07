args <- commandArgs(TRUE)
chr <- unlist(strsplit(args[1], ","))
start_df <- as.numeric(unlist(strsplit(args[2], ",")))
flna <-args[3]

# chr = c("chr2", "chr10", "chr1")
# start_df = c(135262159, 92631683, 154437881)
# flna = "asfsdvrs.txt"

end_df <- numeric()
for(i in start_df){
  end_df <- append(end_df, i+1)
}

library(rtracklayer)
setwd("/var/www/html/R_file")
chainObject <- import.chain("hg38ToHg19.over.chain")
grObject_usng_array <- GRanges(seqnames=chr, ranges=IRanges(start=start_df, end=end_df))
rsnt_usng_DF <- as.data.frame(liftOver(grObject_usng_array, chainObject))
setwd("/var/www/html/qtl_Output")
write.table(rsnt_usng_DF, file = flna, sep = '\t', row.names = FALSE)
# print('Everything is working!!!!!!!')