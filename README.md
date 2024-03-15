# SNPversity2.0
SNPversity2.0 is a web-based tool to display maize variant data.

## MaizeGDB instance of SNPversity 2.0

https://wgs.maizegdb.org/

## Citation

Pending.

## Browser Compatibility 

SNPVersity was developed using a JavaScript framework, ensuring that most of its functionalities operate directly within your local browser. To guarantee optimal performance and a seamless user experience, we have tested and fine-tuned the tool for compatibility with the latest versions of Google Chrome and Mozilla Firefox. However, users accessing SNPversity via Safari or other browsers might experience some performance inconsistencies. Should you encounter any issues, please do not hesitate to ask for assistance.

## SNPversity code
The SNPversity uses a combination of HTML, JavaScript, and CSS for the front-end development.  There are two PHP files and a Python script that make up the server side application.  The variant data is stored in HDF5 databases and called through Python.  The gene model coordinates are stored in a servialized PHP array. 

### Requirements
The best way to run a local version of SNPversity is to download the Docker container and HDF5 databases.  If you want to run the source code directly, you will need a web server (e.g. Apache) that runs PHP and Python.  The code was developed and tested on PHP 8.2.8 and Python 3.9.12.  The Python script uses packages h5py, sys, json, and numpy.  

### SNPversity website directory structure

```bash
SNPversity/
├── css
    └── main.css
├── gff
    └── genes_data.serialized
├── gif
    └── loading.gif
├── hdf5
├── js
    ├──header_data.js
    ├──main.js
    └── VCF2PopTree.js
├── vcf
├── h5_to_vcf.py
├── index.html
├── lookupGeneModel.php
├── maize_accessions.txt
└── maize_accessions.tsv

```
### Description of code

| File                    | Description                                                                           |
|-------------------------|---------------------------------------------------------------------------------------|
|index.html | The main HTML code that provides the core framework of the website. |
|h5_to_vcf.py | Python script that queries the HDF5 database based on the genomic region and then filters the results on the selected maize accessions.|
|lookupGeneModel.php | A PHP script that returns the chromosome, start, and end positions of a given gene model in B73 RefGen_v5.|
|maize_accessions.txt and maize_accessions.tsv | A list of the accession names, GenBank SRA ID, BioProject, and the unique name used in SNPversity.|
|main.css | The main CSS file for styling the webpage.|
|genes_data.serialized | A serialized PHP array storing the coordinates of the B3 RefGen_v5 gene models.|
|main.js | The core JavaScript code used for visualization and other functinality of the website.|
|VCF2PopTree.js | Modified JavaScript code from the VCF2PopTree project used for drawing phylogenetic trees.|
|header_data.js | A JavaScript file containing an array of the column headers of a fulle VCF entry.|
|vcf [directory] | The directory where VCF files are stored. The VCF files are generated by the user's queries and used to build the tables and trees.  The Docker image has built-in CRON jobs to periodically clean the contents of the directory.|
|hdf5 [directory] | The directory stores the HDF5 databases for each database type (high coverage and high quality). There is a separate database for each chromosome.|


# History

# Hapmap 5
A new haplotype map (HapMap) for maize was generated using a diverse set of inbred lines, landraces, and teosintes from 1,498 public resequenced lines through a standardized variant-calling pipeline against version 5 of the B73 reference genome. The output was filtered for mapping quality, coverage, and linkage disequilibrium, and annotated based on variant effects relative to the B73 RefGen_v5 gene annotations.  Two versions of the dataset are available.  A high-coverage dataset consisting of ~230 million loci was filtered on mapping quality and coverage. A high-quality dataset of ~75 million loci had an additional filtering step based on high confident linkage disequilibrium.  See tables below to see the projects used to build the dataset and a summary of the variant effect annotations.

|Accessions | Project |Project name|	
|---------------|--------------|--------------|
|539 |PRJCA009749 |WGS resequencing of 1,604 maize inbred lines	 |
|521 |PRJNA531553 |Deep DNA resequencing of the association mapping panel |	
|340 |PRJNA783885 |Maize landrace whole genome resequencing	 |
|35 |PRJEB56320 |Maize Wisconsin Diversity Panel Resequencing |	
|32 |PRJNA609577 |Zea mays Genome sequencing |	
|11 |PRJNA389800 |Whole genome sequencing of maize 282 panel |	
|8 |PRJEB14212 |European maize diversity |	
|7 |PRJNA399729 |Maize Haplotype Map version 3 |	
|6 |PRJNA260788 |Zea mays Genome sequencing (European maize genomes) |	
|1 |PRJEB56265 |Resequencing of three Polish maize inbred lines |	
The table shows the composition of public resequencing data used to build the dataset.  The table lists the number of accessions from each NCBI bioproject.

|Effect | High Coverage | High Quality |
|---------------|--------------|--------------|
|intergenic |216,128,332 |69,236,257|
|5' UTR | 544,839 | 287,968|
|synonymous |1,042,272 |523,102|
|missense |1,311,927 |572,404|
|stop |61,129  |20,049|
|frameshift |138,463 |40,550|
|intron |8,601,103 |4,349,068|
|non-coding	|3,708 |1,978|
|3'UTR |801,323 |468,603|
|other |46,355 |18,411|
|TOTAL |228,679,451 |75,518,390|
The table shows the variant effect dataset.  The table the type of variant effect for the high-coverage and high-quality datasets.


# Data Processing

## Step 1: Filtering
The first step strips the raw VCF of any unneeded metadata and filters each locus by mapping quality (MQ >= 30), coverage (COV >= 50%), and removing multi-allelic loci.

|Terms | Abbreviation | Definition | 
|---------------|--------------|--------------|
|Mapping Quality | MQ | The average mapping quality of reads supporting the variant. | 
|Coverage Count | CVC |The number of genotypes with at least one read at the given variant. | 
|Coverage Percent | CVP |The percent of genotypes with at least one read at the given variant. | 

Filter criteria  
* MQ >= 30  
* CVP >= 50  
* Remove multiallelic variants.  

|Usage | Description | 
|---------------|--------------|
|script| filter_and_clean.py | 
|input| A unfiltered VCF file.  |
|output| A filtered VCF file with MQ and COV in the INFO field and 'GT' formated alleles.  |
|sample usage| pyhon filter_and_clean.py chr1_unfiltered.vcf chr1_filtered.vcf|  

## Step 2: Annotation
The second step requires the installation of SNPEff.  SNPEff is used to annotate the variant effects for each locus against a reference genome. 

|Terms | Abbreviation | Definition | 
|---------------|--------------|--------------|
|Gene model name | GENEMODEL | The name of the gene model affected by the variant. Uses the closest gene models when the variant is "intergenic”.  |
|Putative impact | EFFECT | A simple estimation of putative impact/deleteriousness: {HIGH, MODERATE, LOW, MODIFIER}.   |
|Annotation type | TYPE |  The type of effect using Sequence Ontology terms.  |

|Usage | Description | 
|---------------|--------------|
|script | snpEff.jar  |
|input | The filterd VCF file from step 1. | 
|output: | A SNPEff annotated VCF file. | 
|sample usage | java -Xmx100g -jar snpEff.jar maize chr1_filtered.vcf > chr1_snpeff.vcf |


## Step 3: Filter on Linkage Disequilibrium

The third step requires the installation of PLINK.  PLINK is used to identify linkage disequilbrium between the variant loci.

Filter criteria

* Linkage distance between 400bp to 5000bp  
 max R2 >= 0.5  

|Terms | Abbreviation | Definition | 
|---------------|--------------|--------------|
|Squared correlation | R2 | The square of the correlation coefficient between pairs of loci on a chromosome. It is a measure of the degree of association or linkage disequilibrium between the alleles at the two loci. |
|Maximum Squared correlation | MAXR2 | The maximum R2 for a given loci | 

|Usage | Description | 
|---------------|--------------|
|script | snpEff.jar  |
|input | The filterd VCF file from step 1. | 
|output: | A SNPEff annotated VCF file. | 
|sample usage | java -Xmx100g -jar snpEff.jar maize chr1_filtered.vcf > chr1_snpeff.vcf |




## Step 4: Clean VCFs

|Column name | Definition | Abbreviation |  Example data|
|---------------|--------------|--------------|--------------| 
|Chromosome |CHROM | The chromosome where the locus is located | chr1 |   
|Position |POS| The genomic coordinate on the chromosome | 104985| 
|Identifier |ID| The value of the IDs are '.'.|
|Reference allele  | REF | The allele value for the locus in the reference genome B73. | A |
|Alternate allele  | ALT | The alternative allele value found in other maize accessions | T | 
|Quality score  | QUAL | The average mapping quality of reads supporting the variant.
|Filter | FILTER | The value of the FILTERs are '.'.|
|Information | INFO | TEST|
|Allele format | FORMAT | MQ=58.01;CVC=1486;CVP=99.20 |

The following values are found in the Information field: 
* GENEMODEL (e.g. Zm00001eb430510)  
* EFFECT (e.g. HIGH, MODERATE, LOW, MODIFIER)  
* TYPE (e.g.  intergenic, synonymous_variant, missense_variant, frameshift_variant, 3_prime_UTR_variant, etc.)  
* MQ (e.g. 47.19)  
* CVC (e.g.1109)   
* CVP (e.g. 74.03)  
* MAXR2 (0.68417)  // MAXR2 is available in the high-quality dataset

There are also columns (named based on the accession name, an undercore, and the SRR ID) for each of the ~1500 accessions in Genotype (GT) format:  
* 0/0 indicates a homozygous reference genotype.  
* 0/1 or 1/0 indicates a heterozygous genotype with one reference and one alternate allele.
*  1/1 indicates a homozygous alternate genotype.  
* ./. missing or unknown genotypes.  


## Step 5: Summary and Statistics

Get a summary and statistics on the data   
Gene model summary includes counts of the following types of annotations: total, intergenic, upstream, 5' UTR, synonymous, missense, stop, frameshift, intron, non_coding, 3' UTR, downstream, other   
Statistics include:  
total SNPs/INDELS  
Count of homozygous alleles  
Count of heterozygous alleles  
Count of missing alleles  
Number of SNPs with at least one heterozygous allele  
Count of Deletions  
Count of Insertions  
Count of loci with Mapping Quality with (30 <= MQ < 40)	 
Count of loci with Mapping Quality with (40 <= MQ < 50)	 
Count of loci with Mapping Quality with (50 <= MQ < 60)	  
Count of loci with Coverage Percentage with (50 <= CVP < 60)  
Count of loci with Coverage Percentage with (60 <= CVP < 70)  
Count of loci with Coverage Percentage with (70 <= CVP < 80)  
Count of loci with Coverage Percentage with (80 <= CVP < 90)  
Count of loci with Coverage Percentage with (90 <= CVP <= 100)  
Count of loci with max R2 with (50 <= MAXR2 < 60)	
Count of loci with max R2 with (60 <= MAXR2 < 70)		
Count of loci with max R2 with (70 <= MAXR2 < 80)		
Count of loci with max R2 with (80 <= MAXR2 < 90)	  	
Count of loci with max R2 with (90 <= MAXR2 <= 100)  


## Step 6: Filtering

## Optional: Prepare data for PanEffect


