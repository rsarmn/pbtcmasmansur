# 🚀 QUICK REFERENCE CARD

## Sistem Penginapan PESMA - Cheat Sheet

---

## 🔑 Login Credentials
```
Email: admin@example.com
Password: password123
```

---

## 🌐 URLs

| Page | URL | Description |
|------|-----|-------------|
| **Login** | `/login` | Admin login page |
| **Dashboard** | `/admin/dashboard` | Statistics overview |
| **Data Kamar** | `/admin/kamar` | Room management |
| **Data Pengunjung** | `/admin/pengunjung` | Guest management |

---

## 🗂️ Database Info

### Sample Data (After Seeding)
- **Rooms**: 6 total (4 available, 2 occupied)
- **Guests**: 3 total (2 Individu, 1 Corporate)
- **Admin**: 1 user account

### Tables
```sql
users           - User accounts with roles
admins          - Admin specific data
kamars          - Room inventory
pengunjungs     - Guest records
```

---

## ⚡ Quick Commands

### Start Server
```bash
cd /Applications/MAMP/penginapan
php artisan serve
# Access: http://localhost:8000
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Check Routes
```bash
php artisan route:list --path=admin
```

### Database Inspection
```bash
php artisan tinker
>>> \App\Models\Kamar::count()
>>> \App\Models\Pengunjung::count()
>>> \App\Models\Admin::first()
```

---

## 🎨 Design Tokens

```css
/* Colors */
--brand: #b3123b
--brand-2: #d23b57
--soft: #efb09b
--pink-header: #f5d7de
--pink-text: #7b1a2e

/* Spacing */
Border radius: 16-28px
Card padding: 28-32px
Table padding: 12px

/* Typography */
Stat value: 32px, weight 800
Stat label: 15px, opacity 0.95
Header text: Bold, 2-line layout
```

---

## 📊 Statistics Formulas

### Kamar Tersedia (%)
```php
$percentage = round(($kamarKosong / $totalKamar) * 100)
```

### Kamar Kosong (%)
```php
$percentage = round((($totalKamar - $kamarKosong) / $totalKamar) * 100)
```

### Jumlah Pengunjung
```php
$count = Pengunjung::count()
```

---

## 🛠️ File Locations

### Controllers
```
app/Http/Controllers/
├── AdminController.php      # Dashboard
├── AuthController.php       # Login/Logout
├── KamarController.php      # Room CRUD
└── PengunjungController.php # Guest CRUD
```

### Views
```
resources/views/
├── layout.blade.php         # Main layout
├── admin/
│   ├── dashboard.blade.php  # Stats cards
│   ├── kamar.blade.php      # Room table
│   └── pengunjung.blade.php # Guest table
└── auth/
    └── login.blade.php      # Login form
```

### Assets
```
public/img/
├── logo-pesma.svg           # Brand logo
└── illustration-admin.svg   # Login illustration
```

---

## 🔧 Troubleshooting

| Problem | Solution |
|---------|----------|
| Can't login | Check seeder ran, email/password correct |
| Logo missing | Verify `public/img/logo-pesma.svg` exists |
| No data in tables | Run `php artisan migrate:fresh --seed` |
| 419 CSRF error | Clear browser cache, check `@csrf` in forms |
| Redirect loop | Run `php artisan session:clear` |
| Blob shapes missing | Check CSS variables loaded in layout |

---

## 📱 Testing Workflow

1. **Login** → Enter credentials → Click LOGIN
2. **Dashboard** → Verify 3 stat cards → Click "Data Kamar"
3. **Kamar** → See 6 rooms → Click "Hapus" → Confirm
4. **Back** → Click "Kembali" → Dashboard loads
5. **Pengunjung** → Click "Data Pengunjung" → See 3 guests
6. **Logout** → Click "Logout" → Return to login

---

## 🎯 Feature Checklist

- [x] Authentication (login/logout)
- [x] Dashboard with statistics
- [x] Room listing with delete
- [x] Guest listing with delete
- [x] Navigation between pages
- [x] Responsive design
- [x] Professional UI matching screenshots
- [x] Sample data seeding
- [x] CSRF protection
- [x] Session management

---

## 📞 Support Files

- `README.md` - Complete setup guide
- `TESTING.md` - Detailed test cases
- `PROJECT_SUMMARY.md` - Full project overview

---

## 🚦 Status Indicators

### Current Status: ✅ **PRODUCTION READY**

- ✅ All migrations run
- ✅ Seeders working
- ✅ All routes registered
- ✅ No compile errors
- ✅ UI matches screenshots
- ✅ Authentication functional
- ✅ CRUD operations working

---

## 💡 Pro Tips

1. **Always use `php artisan migrate:fresh --seed`** after pulling changes
2. **Check console** in browser DevTools for JS errors
3. **Use `php artisan tinker`** to inspect data quickly
4. **Clear cache** when something looks wrong
5. **Check `.env`** file for correct database credentials

---

**Last Updated**: October 17, 2025  
**Version**: 1.0.0  
**Framework**: Laravel 11.x + Tailwind CSS
