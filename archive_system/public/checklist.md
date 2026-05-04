# 📋 University Data Archive System — Master Checklist (Updated)

---

## 🧱 1. Core System (Foundation)

- [x] Database created  
- [x] Users table  
- [x] Institutions table  
- [x] Uploads table  
- [ ] Courses table *(optional next phase)*  

---

## 🔐 2. Authentication System

- [x] User registration (multi-step)  
- [x] Login system  
- [x] Password hashing  
- [x] Session handling  
- [x] Logout system  
- [x] Protected routes  

---

## 👤 3. User System

- [x] Roles (student/staff)  
- [x] Institution assignment  
- [x] User session data (name, role, institution)  

### Improvements
- [ ] Profile page  
- [ ] Edit profile  
- [ ] Profile image upload  

---

## 📤 4. Upload System

- [x] File upload system  
- [x] File validation (type + size)  
- [x] Institution linking  
- [x] Visibility (public / institution / private)  
- [x] Secure filename generation  

---

## 📥 5. Download System (UPDATED)

- [x] Secure download by ID  
- [x] Download tracking system (logs user + file)  
- [x] Access logging via downloads table  
- [ ] Download analytics (most downloaded files)  

---

## ⭐ 6. Saved / Favorites / Bookmark System (NEW)

### Database
- [ ] Create `saved` table  
  - `id`  
  - `user_id`  
  - `upload_id`  
  - `created_at`

### Core Features
- [ ] Save / bookmark file  
- [ ] Unsave / remove bookmark  
- [ ] Prevent duplicate saves  
- [ ] Check saved state per file  

### UI Features
- [ ] Save button on file cards  
- [ ] Toggle state (Save / Saved)  
- [ ] Saved icon in sidebar  
- [ ] Saved items page  

### Saved Page
- [ ] List all bookmarked files  
- [ ] Remove saved items  
- [ ] Search saved items  
- [ ] Filter by file type  

---

## 📂 7. My Uploads Page

- [x] Display user uploads  
- [x] Delete uploads  
- [x] Edit uploads  
- [x] Search uploads  
- [x] Improved UI (badges + layout polish)  

---

## 🌐 8. Library / Browse System

- [x] Global search  
- [x] File type filtering  
- [x] Institution-based filtering  
- [x] Visibility rules  

### Improvements
- [ ] Advanced filters (size, date range, popularity)  
- [ ] Most downloaded sorting  
- [ ] Most saved sorting  

---

## 🎨 9. UI / UX

- [x] Bootstrap layout  
- [x] Responsive design  
- [x] Improved file cards  
- [x] Icon-based actions  
- [x] Clean badge system  

### Improvements
- [ ] Empty states design  
- [ ] Loading states  
- [ ] Toast notifications  
- [ ] Save/unsave feedback animation  

---

## 🧭 10. Role-Based Navigation

- [x] Dynamic sidebar base system  
- [x] Shared navigation for all roles  
- [x] Staff-only extra tabs:
  - Earnings  
  - Downloads  
  - Analytics  

---

## 📊 11. Dashboard System

- [x] Role-based stat cards  
- [x] Database-driven metrics  
- [x] Student dashboard stats  
- [x] Staff dashboard stats  

### Improvements
- [ ] Real-time analytics charts  
- [ ] Trending uploads widget  
- [ ] Recent activity from database  

---

## 🛡️ 12. Security

- [x] Prepared statements (partial usage)  
- [x] File validation  
- [x] Upload restrictions  
- [x] Access control for downloads  

### Improvements
- [ ] Rate limiting uploads  
- [ ] Full RBAC permission system  
- [ ] Audit logs for actions  

---

## 🚀 PRIORITY ROADMAP

### NEXT BUILD PHASE

1. ⭐ Saved / Favorites system (DB + UI + toggle logic)  
2. 📊 Dashboard analytics charts (uploads + downloads trends)  
3. 🔥 Saved items sidebar integration  
4. 💰 Staff earnings breakdown per upload  
5. 📈 Top uploads / top downloads leaderboard  

---

## ⚡ CURRENT STAGE

You are now in:

> **Phase 3: Platform Intelligence Layer**

Where the system evolves from:
- Functional system ✔  
- Into smart academic platform ✔  