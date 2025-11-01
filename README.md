# **BeFit AI - Local Setup Guide (Windows)**

This guide will walk you through **cloning**, **setting up PHP and Composer**, installing dependencies (without committing `vendor`), and **importing the database** for the BeFit AI project.

This project requires PHP 8.2 or higher and Composer to install dependencies.
Make sure the PHP zip extension and unzip/7-Zip are enabled/installed to avoid download issues during setup.
If you don’t have PHP installed, download XAMPP and ensure PHP is added to your system PATH.

---

## ✅ **Prerequisites**

### 1. **Install XAMPP (PHP 8.2+)**

* Download from: [https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)
* Use **version 8.2.5 or later** to ensure compatibility.
* Make sure to include **Apache** and **MySQL** during installation.

> 💡 You can verify PHP version via:

```bash
php -v
```

---

### 2. **Install Git (if not already installed)**

* Download and install from: [https://git-scm.com/downloads](https://git-scm.com/downloads)

> After installation, restart your terminal and verify:

```bash
git --version
```

---

### 3. **Install Composer**

* Download from: [https://getcomposer.org/download/](https://getcomposer.org/download/)
* If you're using XAMPP, make sure to point Composer to `C:\xampp\php\php.exe` during installation.

> Check it's working:

```bash
composer --version
```

---

### 4. **Enable Required PHP Extensions**

Open `C:\xampp\php\php.ini` and make sure these lines are **uncommented** (remove the `;` if present):

```ini
extension=zip
extension=mysqli
```

> Then **restart Apache** via the XAMPP Control Panel.

---

## 🚀 **Setting Up the Project**

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-repo/BeFit-AI.git
cd BeFit-AI
```

---

### Step 2: Install PHP Dependencies (via Composer)

If you're a developer or user cloning the project (without the `vendor` folder), run:

#### 🔹 Option A (Recommended): Use `composer_setup.bat`

```bash
composer_setup.bat
```

> This runs `composer install` automatically.

#### 🔹 Option B: Manual Composer Install

```bash
composer install
```

> ⚠️ Requires internet and Composer set up properly (see prerequisites).

---

## 🗃️ **Database Setup (befit\_db)**

### 🔹 Option A: Automatic Setup

Run the bundled setup script:

```bash
cd scripts
setup.bat
```

This will:

* Create the `befit_db` database
* Import the schema and sample data from `database/dump.sql`

📌 **If your MySQL uses a password**, edit `setup.bat` and update:

```bash
mysql -u root -pYOURPASSWORD ...
```

---

### 🔹 Option B: Manual Setup (Fallback)

1. Open **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **New**, name it `befit_db`, then click **Create**.
3. Go to the **Import** tab.
4. Select `database/dump.sql` and run the import.

---

## 🔄 **Updating the Shared Database (For Developers)**

If you make schema changes (new tables/columns), update `dump.sql`:

```bash
cd scripts
publish.bat
```

Then **commit `database/dump.sql`** to version control so others can sync your changes.

---

## 🧠 **Troubleshooting**

| Issue                                         | Fix                                                    |
| --------------------------------------------- | ------------------------------------------------------ |
| **"Class 'GeminiAPI\Client' not found"**      | Run `composer install` to generate the `vendor` folder |
| **"git not found" error**                     | Install Git and ensure it’s in your system PATH        |
| **Composer fails due to missing ZIP support** | Enable `extension=zip` in `php.ini`, or install 7-Zip  |
| **MySQL connection errors in `setup.bat`**    | Ensure MySQL is running and credentials are correct    |
| **php not recognized**                        | Add `C:\xampp\php` to your system PATH                 |

---

## 📂 **Committing Guidelines**

### 🚫 Do NOT commit `/vendor` folder

To prevent this:

1. Add this to `.gitignore`:

```
/vendor/
```

2. Remove it from Git if already committed:

```bash
git rm -r --cached vendor
git commit -m "Remove vendor folder from repo"
```

> All users should now run `composer install` or `composer_setup.bat` after cloning.

---

## ✅ **Deployment Checklist**

| Task                 | File/Command                    |
| -------------------- | ------------------------------- |
| Clone project        | `git clone ...`                 |
| Install dependencies | `composer_setup.bat`            |
| Set up database      | `scripts/setup.bat`             |
| Start XAMPP services | Apache & MySQL                  |
| Launch site          | `http://localhost/BeFit-Folder` |

---