# SNPversity2.0
SNPversity2.0 is a web-based tool to display maize variant data.

## MaizeGDB instance of SNPversity 2.0

https://www.maizegdb.org/effect/maize/

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

index.html : The main HTML code that provides the core framework of the website.
h5_to_vcf.py : Python script that queries the HDF5 database based on the genomic region and then filters the results on the selected maize accessions.
lookupGeneModel.php : A PHP script that returns the chromosome, start, and end positions of a given gene model in B73 RefGen_v5.
maize_accessions.txt and maize_accessions.tsv: A list of the accession names, GenBank SRA ID, BioProject, and the unique name used in SNPversity.
main.css : The main CSS file for styling the webpage.
genes_data.serialized : A serialized PHP array storing the coordinates of the B3 RefGen_v5 gene models.
main.js : The core JavaScript code used for visualization and other functinality of the website.
VCF2PopTree.js : Modified JavaScript code from the VCF2PopTree project used for drawing phylogenetic trees.
header_data.js : A JavaScript file containing an array of the column headers of a fulle VCF entry.
vcf [directory]: The directory where VCF files are stored. The VCF files are generated by the user's queries and used to build the tables and trees.  The Docker image has built-in CRON jobs to periodically clean the contents of the directory.
hdf5 [directory]: The directory stores the HDF5 databases for each database type (high coverage and high quality). There is a separate database for each chromosome.


# History

# Hapmap 5

# Data Processing

## Step 1: Filtering

## Step 2: Annotation

## Step 1: Linkage Disequilibrium

## Step 1: Clean VCFs

## Step 1: Summary and Statistics

## Step 1: Filtering

## Optional: Prepare data for PanEffect


