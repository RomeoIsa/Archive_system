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

## 📥 5. Download System

- [x] Secure download by ID  
- [x] Download tracking system (logs user + file)  
- [x] Access logging via downloads table  
- [ ] Download analytics (most downloaded files)  

---

## ⭐ 6. Saved / Favorites / Bookmark System

### Database
- [x] Create `saved` table  
  - `id`  
  - `user_id`  
  - `upload_id`  
  - `created_at`  
  - Unique constraint (user_id, upload_id)

---

### Core Features
- [x] Save / bookmark file  
- [x] Unsave / remove bookmark  
- [x] Prevent duplicate saves  
- [x] Check saved state per file  

---

### UI Features
- [x] Save button on file cards  
- [x] Toggle state (Save / Saved)  
- [x] Live toggle without page reload (AJAX)  
- [x] Saved icon/button integration in library  

---

### Saved Page
- [x] List all bookmarked files  
- [x] Remove saved items  
- [x] Search saved items  
- [x] Filter by file type  
- [x] Match full library UI (badges, metadata, polish)  
- [x] Add saved star indicator  
- [x] Add animated remove interaction  

---

## 🕒 7. Recent Activity System

### Database
- [x] Create `recent_views` table  
  - `id`  
  - `user_id`  
  - `upload_id`  
  - `viewed_at`  

---

### Core Features
- [x] Track viewed files  
- [x] Prevent duplicate recent entries  
- [x] Update latest view timestamp  
- [x] View history per user  

---

### Dashboard Features
- [x] Recently viewed widget  
- [x] Latest 5 viewed files  
- [x] View All button  
- [x] Database-driven recent activity  

---

### Recent Activity Page
- [x] Full recent activity page  
- [x] Group viewed files by date  
- [x] Today / Yesterday sections  
- [x] Match library card styling  
- [x] Open Again button  
- [x] Chronological ordering  

---

## 📂 8. My Uploads Page

- [x] Display user uploads  
- [x] Delete uploads  
- [x] Edit uploads  
- [x] Search uploads  
- [x] Improved UI (badges + layout polish)  

---

## 🌐 9. Library / Browse System

- [x] Global search  
- [x] File type filtering  
- [x] Institution-based filtering  
- [x] Visibility rules  
- [x] Saved state detection per file  

### Improvements
- [ ] Advanced filters (size, date range, popularity)  
- [ ] Most downloaded sorting  
- [ ] Most saved sorting  
- [ ] Save count display  

---

## 🎨 10. UI / UX

- [x] Bootstrap layout  
- [x] Responsive design  
- [x] Improved file cards  
- [x] Icon-based actions  
- [x] Clean badge system  
- [x] Consistent card layout across pages  

### Improvements
- [ ] Empty states design  
- [ ] Loading states  
- [ ] Toast notifications  
- [ ] Save/unsave animation feedback  
- [ ] Dashboard skeleton loaders  

---

## 🧭 11. Role-Based Navigation

- [x] Dynamic sidebar base system  
- [x] Shared navigation for all roles  
- [x] Recent Activity sidebar link  
- [x] Active page highlighting  

### Staff-only extra tabs
- [x] Earnings  
- [x] Downloads  
- [x] Analytics  

---

## 📊 12. Dashboard System

- [x] Role-based stat cards  
- [x] Database-driven metrics  
- [x] Student dashboard stats  
- [x] Staff dashboard stats  
- [x] Recently viewed section  
- [x] Database-powered recent widget  

### Improvements
- [ ] Real-time analytics charts  
- [ ] Trending uploads widget  
- [ ] Download trend graphs  
- [ ] Personalized recommendations  

---

## 🛡️ 13. Security

- [x] Prepared statements (partial usage)  
- [x] File validation  
- [x] Upload restrictions  
- [x] Access control for downloads  
- [x] Access control for file viewing  

### Improvements
- [ ] Rate limiting uploads  
- [ ] Full RBAC permission system  
- [ ] Audit logs for actions  
- [ ] CSRF protection  
- [ ] Secure MIME verification  