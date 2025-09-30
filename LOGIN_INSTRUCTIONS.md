## 🔐 Admin Login Instructions

**To see MongoDB products in the admin panel, you need to log in as an admin first.**

### Quick Login:
1. Go to: http://127.0.0.1:8000/login
2. Use these credentials:
   - **Email**: `admin@vosiz.com`
   - **Password**: `admin123`

3. After login, go to: http://127.0.0.1:8000/admin/products/manage

### Alternative Direct Access (No Authentication Required):
- **Debug Products Page**: http://127.0.0.1:8000/products-debug
- **MongoDB Test Data**: http://127.0.0.1:8000/mongo-test
- **Auth Status Check**: http://127.0.0.1:8000/auth-check

### MongoDB Products Status:
- ✅ **20 products** are stored in MongoDB
- ✅ **Connection working** perfectly
- ✅ **Admin interface** ready
- ⚠️ **Login required** to access admin panel

### Troubleshooting:
If you still can't see products after login:
1. Clear browser cache
2. Try incognito/private browsing
3. Check `/products-debug` page for direct access

### Next Steps After Login:
1. View all MongoDB products
2. Edit products to add images
3. Test product creation with file uploads