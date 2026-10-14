# Face Recognition Attendance System — Local Deployment & HTTPS Setup Guide

## 1. System Requirements & Stack
- **OS**: Windows 10 / 11 with XAMPP (Apache 2.4, MariaDB 10.4+, PHP 8.2.12 ZTS x64).
- **PHP Extensions Required**: `pdo_mysql`, `mbstring`, `openssl`, `json`.
- **Database**: MariaDB / MySQL on port `3307` (or default `3306`).
- **Path**: `D:\xampp\htdocs\project se\`

---

## 2. Apache VirtualHost & Directory Configuration

Add the following block to `D:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "D:/xampp/htdocs/project se/public"
    ServerName face-attendance.local
    ServerAlias localhost
    
    <Directory "D:/xampp/htdocs/project se/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/face_attendance_error.log"
    CustomLog "logs/face_attendance_access.log" combined
</VirtualHost>
```

---

## 3. HTTPS Setup for Mobile Camera Access (LAN Access)

> **Why HTTPS?** Modern web browsers on mobile devices (Android Chrome, iOS Safari) strictly block camera access (`getUserMedia`) on unencrypted HTTP unless accessed via exact `localhost`. For mobile devices connecting via local Wi-Fi LAN IP, HTTPS is mandatory.

### Step 3.1: Generate Local OpenSSL Certificate
Open Command Prompt / PowerShell as Administrator:

```powershell
cd D:\xampp\apache
openssl req -new -x509 -newkey rsa:2048 -nodes -keyout conf/ssl.key/server.key -out conf/ssl.crt/server.crt -days 365 -subj "/CN=192.168.1.100"
```
*(Replace `192.168.1.100` with your machine's local LAN IPv4 address from `ipconfig`)*.

### Step 3.2: Configure Apache SSL (`httpd-ssl.conf`)
Ensure `conf/extra/httpd-ssl.conf` has SSL enabled:

```apache
<VirtualHost *:443>
    DocumentRoot "D:/xampp/htdocs/project se/public"
    ServerName 192.168.1.100:443
    SSLEngine on
    SSLCertificateFile "conf/ssl.crt/server.crt"
    SSLCertificateKeyFile "conf/ssl.key/server.key"
    
    <Directory "D:/xampp/htdocs/project se/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Restart Apache via XAMPP Control Panel.

---

## 4. Mobile Phone Connection Guide
1. Connect mobile phone to the **same Wi-Fi network** as the host PC.
2. Open browser on phone and navigate to: `https://<YOUR_LAN_IP>/project%20se/public/`
3. Accept the self-signed SSL certificate warning (*Proceed to site*).
4. Navigate to **Mark Attendance** or **Register Face** and allow camera permission.

---

## 5. Automated Absence Generation Setup (Windows Task Scheduler)
To run the automated absence generator daily at 23:59:

1. Open **Windows Task Scheduler** → *Create Task*.
2. **General**: Name: `FaceAttendanceAbsenceCron`, Run whether user is logged on or not.
3. **Triggers**: Daily at 11:59 PM.
4. **Actions**: *Start a Program*:
   - Program/script: `D:\xampp\php\php.exe`
   - Add arguments: `D:\xampp\htdocs\project se\cron\generate_absences.php`
   - Start in: `D:\xampp\htdocs\project se\`
