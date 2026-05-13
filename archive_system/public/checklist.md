# 📋 University Data Archive System — Master Checklist (Updated Roadmap)

---

# 🧱 1. Core System (Foundation)

- [x] Database created  
- [x] Users table  
- [x] Institutions table  
- [x] Uploads table  
- [ ] Courses table *(optional next phase)*  
- [ ] Departments table *(future scalability)*  
- [ ] Levels table *(100L / 200L / 300L etc.)*  

---

# 🔐 2. Authentication System

- [x] User registration (multi-step)  
- [x] Login system  
- [x] Password hashing  
- [x] Session handling  
- [x] Logout system  
- [x] Protected routes  

### Improvements
- [ ] Remember me functionality  
- [ ] Email verification *(optional)*  
- [ ] Password reset system  
- [ ] Login activity tracking  

---

# 👤 3. User System

- [x] Roles (student/staff)  
- [x] Institution assignment  
- [x] User session data (name, role, institution)  

---

## ⚙️ Profile Settings System

### Section 1 — Personal Information
- [ ] Profile settings page  
- [ ] Upload/change profile photo  
- [ ] Edit display name  
- [ ] Add/edit matric number  
- [ ] Add/edit department  
- [ ] Add/edit student level  
- [ ] Add/edit bio *(optional)*  

### Section 2 — Account Preferences
- [ ] Theme system (Light/Dark mode)  
- [ ] Appearance customization  
- [ ] Language settings  
- [ ] Notification preferences *(future)*  

### Section 3 — Account Management
- [ ] Save changes button  
- [ ] Delete account option  
- [ ] Account deletion confirmation modal  
- [ ] Logout all devices *(future)*  

---

# 📤 4. Upload System

- [x] File upload system  
- [x] File validation (type + size)  
- [x] Institution linking  
- [x] Visibility (public / institution / private)  
- [x] Secure filename generation  

### Improvements
- [ ] Multiple file upload support *(optional)*  
- [ ] Folder upload support *(optional advanced feature)*  
- [ ] Drag-and-drop uploads  
- [ ] Upload progress bar  
- [ ] Upload queue system  
- [ ] File preview before upload  
- [ ] Chunked uploads for large files  

---

# 📥 5. Download System

- [x] Secure download by ID  
- [x] Download tracking system (logs user + file)  
- [x] Access logging via downloads table  

### Improvements
- [ ] Download analytics (most downloaded files)  
- [ ] Download trends over time  
- [ ] Download history per user  

---

# ⭐ 6. Saved / Favorites / Bookmark System

## Database
- [x] Create `saved` table  
  - `id`  
  - `user_id`  
  - `upload_id`  
  - `created_at`  
  - Unique constraint (`user_id`, `upload_id`)  

---

## Core Features
- [x] Save / bookmark file  
- [x] Unsave / remove bookmark  
- [x] Prevent duplicate saves  
- [x] Check saved state per file  

---

## UI Features
- [x] Save button on file cards  
- [x] Toggle state (Save / Saved)  
- [x] Live toggle without page reload (AJAX)  
- [x] Saved icon/button integration in library  

---

## Saved Page
- [x] List all bookmarked files  
- [x] Remove saved items  
- [x] Search saved items  
- [x] Filter by file type  
- [x] Match full library UI (badges, metadata, polish)  
- [x] Add saved star indicator  
- [x] Add animated remove interaction  

---

# 🕒 7. Recent Activity System

## Database
- [x] Create `recent_views` table  
  - `id`  
  - `user_id`  
  - `upload_id`  
  - `viewed_at`

---

## Core Features
- [x] Track viewed files  
- [x] Prevent duplicate recent entries (update timestamp instead)  
- [x] Update latest view timestamp on re-open  
- [x] View history per user  

Current behavior already resets/updates the timestamp to the newest date and time whenever a user reopens a file.  
That means if a file was viewed last week and opened again today, it moves back to the top as “recently viewed.”

---

## Dashboard Features
- [x] Recently viewed widget  
- [x] Limit to 5 latest viewed files  
- [x] View All button linking to full page  
- [x] Recently uploaded widget (latest 5 uploads)  
- [x] Database-driven activity system  

---

## Recent Activity Page
- [x] Full activity page (all viewed files)  
- [x] Group by date (Today / Yesterday / Previous dates)  
- [x] Chronological ordering  
- [x] Open Again button  
- [x] Clean card-based UI  
- [x] Date headers for sections  

---

# 📂 8. My Uploads Page

- [x] Display user uploads  
- [x] Delete uploads  
- [x] Edit uploads  
- [x] Search uploads  
- [x] Improved UI (badges + layout polish)  

### Improvements
- [ ] Bulk delete uploads  
- [ ] Bulk visibility edit  
- [ ] Upload statistics per file  
- [ ] Recently uploaded highlight  

---

# 🌐 9. Library / Browse System

- [x] Global search  
- [x] File type filtering  
- [x] Institution-based filtering  
- [x] Visibility rules  
- [x] Saved state detection per file  

---

## Academic Filtering System

- [ ] Student level filter (100L / 200L / 300L etc.)  
- [ ] Department filter  
- [ ] Course code filter  
- [ ] Semester filter *(future)*  
- [ ] Faculty filter *(future)*  

### Recommended Logic
- [ ] Allow users to optionally choose their level during profile setup  
- [ ] Auto-prioritize files matching their level  
- [ ] Still allow manual filtering for all levels  

---

## Improvements
- [ ] Advanced filters (size, date range, popularity)  
- [ ] Most downloaded sorting  
- [ ] Most saved sorting  
- [ ] Save count display  
- [ ] Recently uploaded sorting  
- [ ] Smart search suggestions  
- [ ] Recommended files section  

---

# 🎨 10. UI / UX

- [x] Bootstrap layout  
- [x] Responsive design  
- [x] Improved file cards  
- [x] Icon-based actions  
- [x] Clean badge system  
- [x] Consistent card layout across pages  
- [x] Dashboard activity panels (2-column layout)  

---

## UI Refinement Goals

### General Polish
- [ ] Refine spacing and typography  
- [ ] Improve color consistency  
- [ ] Add smoother hover animations  
- [ ] Improve sidebar aesthetics  
- [ ] Better mobile responsiveness  
- [ ] Cleaner empty states  
- [ ] Modern glassmorphism/card effects *(optional)*  

### Feedback & Interactions
- [ ] Loading states  
- [ ] Toast notifications  
- [ ] Save/unsave animation feedback  
- [ ] Dashboard skeleton loaders  
- [ ] Smooth page transitions  

### File Experience
- [ ] File preview modal  
- [ ] Better file type icons  
- [ ] Thumbnail previews for supported files  
- [ ] PDF inline viewer  
- [ ] Image gallery viewer  

---

# 🧭 11. Role-Based Navigation

- [x] Dynamic sidebar base system  
- [x] Shared navigation for all roles  
- [x] Recent Activity sidebar link  
- [x] Active page highlighting  

---

## Staff-only Extra Tabs
- [x] Earnings  
- [x] Downloads  
- [x] Analytics  

### Improvements
- [ ] Role-specific dashboard widgets  
- [ ] Admin-only moderation panel *(future)*  

---

# 📊 12. Dashboard System

- [x] Role-based stat cards  
- [x] Database-driven metrics  
- [x] Student dashboard stats  
- [x] Staff dashboard stats  
- [x] Recently viewed widget (limit 5)  
- [x] Recently uploaded widget (limit 5)  
- [x] Fully dynamic activity overview  

---

## Improvements
- [ ] Real-time analytics charts  
- [ ] Trending uploads widget  
- [ ] Download trend graphs  
- [ ] Personalized recommendations  
- [ ] Most active users panel  
- [ ] Popular departments widget  

---

# 🛡️ 13. Security

- [x] Prepared statements (partial usage)  
- [x] File validation  
- [x] Upload restrictions  
- [x] Access control for downloads  
- [x] Access control for file viewing  
- [x] Recent activity tied to user session  

---

## Improvements
- [ ] Rate limiting uploads  
- [ ] Full RBAC permission system  
- [ ] Audit logs for actions  
- [ ] CSRF protection  
- [ ] Secure MIME verification  
- [ ] XSS sanitization improvements  
- [ ] Brute-force login protection  

---

# 📄 14. Expanded File Type Support

## Documents
- [ ] PDF  
- [ ] DOC / DOCX  
- [ ] PPT / PPTX  
- [ ] XLS / XLSX  
- [ ] TXT  
- [ ] RTF  

## Development Files
- [ ] ZIP  
- [ ] RAR  
- [ ] 7Z  
- [ ] JSON  
- [ ] XML  
- [ ] CSV  
- [ ] SQL  

## Media Files
- [ ] JPG / PNG / WEBP  
- [ ] MP4  
- [ ] MP3  
- [ ] WAV  

## Academic Files
- [ ] EPUB  
- [ ] Research datasets  
- [ ] Lecture recordings  
- [ ] Scanned handwritten notes  
- [ ] Project source code archives  

---

# 💡 Strategic Notes

### Multiple File Uploads
This can become a very strong feature because students often upload:
- Entire course materials  
- Past question packs  
- Lecture slide folders  
- Assignment collections  
- Project resources  

Folder uploads especially become valuable when users upload:
- Full semester materials  
- Department archives  
- Grouped lecture content  

This could significantly increase platform usefulness and retention later on.

### Student Level Filtering
You do not *have* to force level selection during signup.  

Best approach:
1. Make level optional in profile settings  
2. Allow filtering manually in search  
3. Use the level later for smarter recommendations and personalization  

That gives flexibility without making onboarding annoying.