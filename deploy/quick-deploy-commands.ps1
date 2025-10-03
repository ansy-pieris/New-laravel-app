# Quick Deployment Commands for PowerShell

# 1. Create a zip file of your application (excluding unnecessary files)
Compress-Archive -Path ".\*" -DestinationPath "apparel_store.zip" -Exclude "node_modules","vendor",".git","storage\logs\*","bootstrap\cache\*"

# 2. Upload to EC2 (replace your-key.pem with your actual key file)
scp -i your-key.pem apparel_store.zip ubuntu@13.204.86.61:/tmp/

# 3. Connect to EC2 and extract
ssh -i your-key.pem ubuntu@13.204.86.61

# Then run these commands on EC2:
# cd /tmp
# sudo apt install unzip -y
# unzip apparel_store.zip -d apparel_store/
# sudo cp -r apparel_store/* /var/www/html/
# sudo bash /var/www/html/deploy/deploy.sh