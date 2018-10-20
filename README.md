# cPanel backup tool

Simple script to backup files and databases directly to s3.

This only works with cPanel. You can use the restore features of cPanel to restore the backups. You can download the backups in the AWS Console. 

## Setup instructions
1. `composer install --no-dev`
1. Change .env.example to .env
1. Modify .env file to your preferences
1. Upload the project to a **non-public** location
1. Setup cron job to backup whenever you would like to

### !! Do not store the project in a publicly accessible location
