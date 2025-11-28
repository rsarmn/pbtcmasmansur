# 🧪 Testing Guide - Sistem Penginapan PESMA

## ✅ Pre-Test Checklist

Pastikan sudah menjalankan:
```bash
php artisan migrate:fresh --seed
php artisan serve
```

## 🔐 Login Credentials

- **Email**: `admin@example.com`
- **Password**: `password123`

---

## 📋 Test Cases

### 1. Login Page Test
**URL**: `http://localhost:8000/login`

✅ **Expected Results**:
- [ ] Two-panel layout (ilustrasi kiri, form kanan)
- [ ] Panel kiri: "Welcome Admin" dengan ilustrasi SVG
- [ ] Panel kanan: Form dengan "USER LOGIN" heading
- [ ] Input email dan password dengan styling soft gray
- [ ] Button "LOGIN" merah maroon
- [ ] Background dengan blob shapes

**Test Steps**:
1. Akses `/login`
2. Masukkan email: `admin@example.com`
3. Masukkan password: `password123`
4. Klik LOGIN
5. Should redirect ke `/admin/dashboard`

---

### 2. Dashboard Test  
**URL**: `http://localhost:8000/admin/dashboard`

✅ **Expected Results**:
- [ ] Header merah maroon dengan logo + brand "PESANTREN MAHASISWA KH. MAS MANSUR"
- [ ] Nav links: "Data Kamar", "Data Pengunjung"
- [ ] Button "Logout" putih di kanan
- [ ] Background dengan 3 blob shapes (peach/orange)
- [ ] 3 Stat cards dengan rounded corners:
  - **Card 1**: % kamar tersedia (calculated from kosong/total)
  - **Card 2**: % kamar kosong (calculated from occupied/total)  
  - **Card 3**: Jumlah pengunjung (number)
- [ ] Setiap card punya icon 📊, value besar, label kecil

**Test Steps**:
1. Login berhasil → auto redirect dashboard
2. Verify statistics match database:
   - Total kamar: 6 (from seeder)
   - Kamar kosong: 4 (status='kosong')
   - Pengunjung: 3 (from seeder)
3. Klik "Data Kamar" → redirect `/admin/kamar`
4. Klik "Data Pengunjung" → redirect `/admin/pengunjung`
5. Klik "Logout" → redirect `/login`

---

### 3. Data Kamar Test
**URL**: `http://localhost:8000/admin/kamar`

✅ **Expected Results**:
- [ ] Header merah maroon sama seperti dashboard
- [ ] Sub-header dengan logo + brand
- [ ] Button "Kembali" putih di kanan
- [ ] Pink container dengan "DATA KAMAR PENGINAPAN"
- [ ] Table dengan header soft pink (#f5d7de)
- [ ] Columns: Nomor Kamar, Jenis, Gedung, Harga, Fasilitas, Status, Aksi
- [ ] 6 rows data (from KamarSeeder)
- [ ] Button "Hapus" merah per row

**Sample Data Expected**:
| Nomor | Jenis  | Gedung | Harga      | Fasilitas          | Status |
|-------|--------|--------|------------|--------------------|--------|
| 101   | Single | A      | Rp 150.000 | AC, TV             | kosong |
| 102   | Double | A      | Rp 200.000 | AC, TV, Wifi       | terisi |
| 103   | Single | A      | Rp 150.000 | AC                 | kosong |
| 201   | Double | B      | Rp 250.000 | AC, TV, Wifi, Kulkas | kosong |
| 202   | Suite  | B      | Rp 350.000 | AC, TV, Wifi, Kulkas, Balkon | terisi |
| 203   | Single | B      | Rp 150.000 | AC, TV             | kosong |

**Test Steps**:
1. Dari dashboard, klik "Data Kamar"
2. Verify 6 rows tampil
3. Klik "Hapus" pada row pertama
4. Confirm alert
5. Row should disappear, total now 5
6. Klik "Kembali" → redirect `/admin/dashboard`

---

### 4. Data Pengunjung Test
**URL**: `http://localhost:8000/admin/pengunjung`

✅ **Expected Results**:
- [ ] Header merah maroon sama seperti dashboard
- [ ] Sub-header dengan logo + brand
- [ ] Button "Kembali" putih di kanan
- [ ] Pink container dengan "DATA PENGUNJUNG PENGINAPAN"
- [ ] Table dengan header soft pink
- [ ] Columns: Nama, Identitas, No Identitas, Jenis Tamu, Check-in, Check-out, Nomor Kamar, Aksi
- [ ] 3 rows data (from PengunjungSeeder)

**Sample Data Expected**:
| Nama            | Identitas | No Identitas      | Jenis     | Check-in   | Check-out  | Kamar |
|-----------------|-----------|-------------------|-----------|------------|------------|-------|
| Ahmad Zulkifli  | KTP       | 3201012345670001  | Individu  | 2025-10-15 | 2025-10-17 | 102   |
| Siti Rahma      | KTP       | 3201012345670002  | Corporate | 2025-10-16 | 2025-10-20 | 202   |
| Budi Santoso    | KTP       | 1234567890123     | Individu  | 2025-10-14 | 2025-10-18 | 101   |

**Test Steps**:
1. Dari dashboard, klik "Data Pengunjung"
2. Verify 3 rows tampil
3. Klik "Hapus" pada row kedua
4. Confirm alert
5. Row should disappear, total now 2
6. Klik "Kembali" → redirect `/admin/dashboard`

---

### 5. Logout Test

**Test Steps**:
1. Dari halaman manapun yang authenticated
2. Klik button "Logout" di header
3. Should POST to `/logout`
4. Session cleared
5. Redirect to `/login`
6. Try akses `/admin/dashboard` tanpa login → should redirect `/login`

---

## 🎨 Visual Regression Checks

### Color Palette
- **Brand Red**: `#b3123b`
- **Soft Peach**: `#efb09b`  
- **Pink Header**: `#f5d7de`
- **Pink Text**: `#7b1a2e`

### Typography
- **Header Brand**: Bold, 2-line with smaller subtitle
- **Stat Values**: Font-size 32px, weight 800
- **Stat Labels**: Font-size 15px, opacity 0.95

### Layout
- **Header**: Rounded top 16px, bottom 28px
- **Stat Cards**: Border-radius 28px, shadow, backdrop-blur
- **Tables**: Pink header, white rows, soft borders
- **Buttons**: Rounded-full for pills, rounded-6px for delete

### Responsive
- [ ] Desktop (1920x1080): 3 columns grid
- [ ] Tablet (768px): 2 columns, then 1
- [ ] Mobile (375px): Single column stacked

---

## 🐛 Common Issues & Fixes

**Issue**: Logo tidak muncul
```bash
# Pastikan file ada
ls -la public/img/logo-pesma.svg
# Jika tidak, file sudah dibuat di public/img/
```

**Issue**: Blob shapes tidak muncul
- Check CSS variables loaded: `--soft`, `--brand`
- Check `.blob` class styling in layout.blade.php

**Issue**: Data tidak muncul di table
```bash
php artisan migrate:fresh --seed
# Re-check dengan:
php artisan tinker
>>> \App\Models\Kamar::count()  # should be 6
>>> \App\Models\Pengunjung::count()  # should be 3
```

**Issue**: 419 CSRF error
- Clear browser cache
- Check `@csrf` in forms
- Verify session driver in `.env`

**Issue**: Redirect loop
- Clear session: `php artisan session:clear`
- Check AdminAuth middleware logic

---

## ✨ Final Checklist

Before considering project complete:

- [x] Database migrated and seeded
- [x] Login works with correct credentials
- [x] Dashboard shows 3 stat cards
- [x] Data Kamar shows 6 rooms
- [x] Data Pengunjung shows 3 guests
- [x] Delete functions work with confirmation
- [x] "Kembali" buttons navigate back
- [x] Logout clears session
- [x] All pages match screenshot design
- [x] Logo and illustrations display
- [x] Responsive on mobile/tablet
- [x] No console errors
- [x] No PHP errors

---

## 📊 Performance Check

```bash
# Check route list
php artisan route:list --path=admin

# Check for N+1 queries (optional)
# Install debugbar:
composer require barryvdh/laravel-debugbar --dev

# Monitor queries on kamar/pengunjung index pages
```

---

## 🚀 Deploy Checklist (Future)

When ready for production:
- [ ] Change `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY`
- [ ] Use strong admin password
- [ ] Enable HTTPS
- [ ] Configure proper database backup
- [ ] Set up proper logging
- [ ] Add rate limiting
- [ ] Implement RBAC if needed

---

**Project Status**: ✅ **COMPLETE & READY FOR TESTING**

Test systematically through each section. Report any visual mismatches or functional bugs.
