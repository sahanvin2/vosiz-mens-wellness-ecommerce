# MongoDB Atlas Database Setup Guide

## 🎯 **Step-by-Step Database Creation**

### **Step 1: Access MongoDB Atlas Dashboard**
1. Go to [https://cloud.mongodb.com/](https://cloud.mongodb.com/)
2. Login with your account
3. Select your project/cluster

### **Step 2: Create Database**
1. Click on **"Browse Collections"** button on your cluster
2. Click **"Create Database"** button
3. Enter the following details:
   - **Database Name**: `vosiz_products`
   - **Collection Name**: `products`
4. Click **"Create"**

### **Step 3: Create Additional Collections**
After creating the database, add these collections:

1. **Reviews Collection**:
   - Click "Create Collection" 
   - Name: `reviews`

2. **Analytics Collection**:
   - Click "Create Collection"
   - Name: `analytics`

3. **Categories Collection**:
   - Click "Create Collection" 
   - Name: `categories`

### **Step 4: Verify Network Access**
1. Go to **"Network Access"** in the left sidebar
2. Click **"Add IP Address"**
3. Choose **"Allow Access from Anywhere"** (0.0.0.0/0)
4. Click **"Confirm"**

### **Step 5: Verify Database User**
1. Go to **"Database Access"** in the left sidebar  
2. Check if user `sahannawarathne2004_db_user` exists
3. If not, click **"Add New Database User"**:
   - Authentication Method: **Password**
   - Username: `vosiz_user`
   - Password: `vosiz123` (simple password)
   - Database User Privileges: **Read and write to any database**
   - Click **"Add User"**

### **Step 6: Get Connection String**
1. Go back to your cluster
2. Click **"Connect"** button
3. Choose **"Connect your application"**
4. Copy the connection string
5. It should look like: 
   ```
   mongodb+srv://vosiz_user:<password>@cluster0.2m8hhzb.mongodb.net/?retryWrites=true&w=majority
   ```

### **Step 7: Test Connection**
Once you have the connection string, we can test it with Laravel.

---

## 🚀 **Alternative: Quick Test Connection**

If you want to test with your existing credentials, let me know:
1. Your exact MongoDB Atlas username
2. Your exact password  
3. The full cluster connection string from Atlas dashboard

Then I can help you connect directly!