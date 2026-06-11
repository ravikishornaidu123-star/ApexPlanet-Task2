# ApexPlanet Internship – Task 3: Advanced Features Implementation

## 📌 Overview
This project is the Task 3 submission for the ApexPlanet 45-Day PHP & MySQL Internship Program.
It builds upon the Task 2 CRUD blog application by adding **search functionality**, **pagination**, and **improved UI**.

---

## ✅ Task 3 Features Implemented

### 1. Search Functionality
- Search bar on the posts listing page
- Search by **All fields**, **Title only**, or **Content only**
- PHP PDO prepared statements for safe search queries
- **Highlighted search terms** shown in results (yellow highlight)
- Result count displayed (e.g. "Found 3 results for 'PHP'")
- Clear search button to reset

### 2. Pagination
- Posts listing shows **5 posts per page**
- Dynamic page number buttons with prev/next navigation
- Ellipsis (`…`) for large page ranges
- Pagination works together with search (preserves query)
- Shows "Page X of Y · Showing N of M posts"

### 3. UI Improvements
- Clean responsive design with custom CSS (no external framework dependency)
- Sticky navbar with active link highlighting
- Post cards with hover effects
- Flash messages (success/error) after actions
- Empty state illustration when no posts exist
- Mobile-responsive layout

---

## 📁 File Structure

```
blog/
├── index.php          ← Posts list + Search + Pagination (Task 3 core)
├── login.php          ← User login
├── register.php       ← User registration
├── create.php         ← Create new post
├── edit.php           ← Edit existing post
├── view.php           ← Single post view
├── delete.php         ← Delete post handler
├── logout.php         ← Session destroy
├── setup.sql          ← Database setup (run this first!)
├── css/
│   └── style.css      ← All custom styles
└── includes/
    ├── db.php          ← PDO database connection
    └── auth.php        ← Session helpers
```

---

## 🚀 Setup Instructions

1. **Start XAMPP** – Run Apache and MySQL services
2. **Import database**:
   - Open `http://localhost/phpmyadmin`
   - Click **Import** and upload `setup.sql`
   - This creates the `blog` database with sample data
3. **Copy project**:
   - Place the `blog/` folder in `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
4. **Open in browser**:
   - Visit `http://localhost/blog/`
   - Login with: **admin** / **admin123**

---

## 🔍 How to Test Search & Pagination

- The `setup.sql` inserts **12 sample posts** (so pagination kicks in at 5/page)
- Try searching for: `PHP`, `MySQL`, `Bootstrap`, `validation`
- Switch the filter dropdown between "All fields", "Title only", "Content only"
- Navigate pages and observe the URL updates with `?page=2&search=php`

---

## 🛡 Security Notes (from Task 2, maintained here)
- Passwords hashed with `password_hash()` / `PASSWORD_BCRYPT`
- All DB queries use **PDO prepared statements**
- Session-based authentication
- Input sanitized with `htmlspecialchars()` before display

---

## 📞 Contact
ApexPlanet Software Pvt. Ltd.
📞 +91 9905879870
📧 info@apexplanet.in
