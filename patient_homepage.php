<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: patient_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PKU Consultation & Appointment</title>
  <link rel="icon" type="photo/images-removebg-preview.png" href="photo/images-removebg-preview.png">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9fafc;
      color: #333;
      line-height: 1.6;
    }

    header {
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 40px;
      border-bottom: 2px solid #eaeaea;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    header img.logo { height: 55px; }

    .welcome {
      background: linear-gradient(90deg, #007bff, #00cfff);
      color: #fff;
      font-size: 26px;
      text-align: center;
      padding: 25px 10px;
      font-weight: bold;
      letter-spacing: 1px;
    }

    /* --- NEW STYLING FOR USER GREETING --- */
    .user-greeting {
      text-align: center;
      margin: 30px 0 20px 0;
      font-size: 32px;
      color: #444;
      font-weight: 600;
    }
    
    .user-greeting span {
        color: #007bff;
        font-weight: 700;
    }
    /* ------------------------------------- */

    .nav-buttons {
      display: flex;
      justify-content: center;
      background: #f1f7fb;
      padding: 15px;
      gap: 15px;
      flex-wrap: wrap;
    }

    .nav-buttons button {
      padding: 12px 24px;
      font-size: 16px;
      background: #007bff;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }

    .nav-buttons button:hover {
      background: #0056b3;
      transform: translateY(-2px);
    }

    .carousel {
      position: relative;
      max-width: 1000px;
      margin: 20px auto;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    .carousel-track { display: flex; transition: transform 0.3s ease-in-out; }
    .carousel img { width: 100%; flex-shrink: 0; border-radius: 12px; }

    .btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0,0,0,0.4);
      color: #fff;
      border: none;
      padding: 8px 12px;
      cursor: pointer;
      border-radius: 50%;
      font-size: 20px;
      z-index: 10;
    }
    .btn.prev { left: 10px; }
    .btn.next { right: 10px; }

    .footer {
      background: #002855;
      color: white;
      padding: 40px 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
    }

    .footer .box { line-height: 1.8; }
    .footer strong { font-size: 16px; }
    .footer a { color: #00cfff; text-decoration: none; }
    .footer a:hover { text-decoration: underline; }

    /* Responsive tweaks */
    @media (max-width: 768px) {
      header { flex-direction: column; text-align: center; }
      .welcome { font-size: 22px; }
      .user-greeting { font-size: 24px; }
    }
  </style>
</head>
<body>

  <header>
    <img src="photo/download.png" alt="UTHM Logo" class="logo">
  </header>

  <div class="welcome">Welcome to PKU Consultation & Appointment</div>
  
  <h1 class="user-greeting">Hello, <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>!</h1>

<div class="nav-buttons">
    <button onclick="window.location.href='patient_view_doctor_list.php'">Doctor</button>
    <button onclick="window.location.href='patient_appointment.php'">Appointment Booking</button>
    <button onclick="window.location.href='patient_check_status.php'">Check Appointment Status</button>
    
    <button onclick="window.location.href='http://localhost:3000'">Consultation</button>
    
    <button onclick="window.location.href='patient_review.php'">Feedback</button>
</div>  

  <div class="carousel">
    <div class="carousel-track">
      <img src="photo/medical2025-80a14506.webp" alt="Medical Operating Hours">
      <img src="photo/dental2025-7b01c3d6.webp" alt="Dental Operating Hours">
      <img src="photo/hubungi kami mei24-1fb2b729.webp" alt="Hubung Kami">
      <img src="photo/emergency mei24-9d1aac72.webp" alt="Emergency">
    </div>
    <button class="btn prev">&#10094;</button>
    <button class="btn next">&#10095;</button>
  </div>

  <div class="footer">
    <div class="box">
      <p><strong>Pusat Kesihatan Universiti (Kampus Parit Raja)</strong><br>
      Universiti Tun Hussein Onn Malaysia<br>
      86400 Parit Raja, Batu Pahat<br>
      Johor, Malaysia<br>
      Talian pertanyaan: +607-453 7846 / 019-392 9849<br>
      Talian Kecemasan: +6019-868 7854<br>
      Fax: +607-453 6077<br>
      Email: <a href="mailto:pku@uthm.edu.my">pku@uthm.edu.my</a></p>
    </div>
    <div class="box">
      <p>Pusat Kesihatan Universiti, Universiti Tun Hussein Onn Malaysia (UTHM) telah ditubuhkan sejak 2002 dengan nama asal Pusat Kesihatan Mahasiswa (PKM).<br>
      Ketika itu PKM diletakkan dibawah Pejabat Hal Ehwal Pelajar (HEP) dan dianggotai oleh 2 orang Jururawat Terlatih, seorang Pembantu Tadbir dan seorang Pembantu Makmal Perubatan.</p>
    </div>
  </div>

  <script>
    class Carousel {
      constructor(trackSelector, prevBtnSelector, nextBtnSelector, interval = 5000) {
        this.track = document.querySelector(trackSelector);
        this.prevBtn = document.querySelector(prevBtnSelector);
        this.nextBtn = document.querySelector(nextBtnSelector);
        this.images = this.track.querySelectorAll('img');
        this.index = 0;
        this.interval = interval;
        this.autoSlide = null;

        if(this.track && this.images.length > 0) {
            this.init();
        }
      }

      init() {
        this.prevBtn.addEventListener('click', () => this.prev());
        this.nextBtn.addEventListener('click', () => this.next());
        this.addTouchSupport();
        this.startAutoSlide();
      }

      update() {
        this.track.style.transform = `translateX(-${this.index * 100}%)`;
      }

      next() {
        this.index = (this.index + 1) % this.images.length;
        this.update();
        this.resetAutoSlide();
      }

      prev() {
        this.index = (this.index - 1 + this.images.length) % this.images.length;
        this.update();
        this.resetAutoSlide();
      }

      startAutoSlide() {
        this.autoSlide = setInterval(() => {
          this.next();
        }, this.interval);
      }

      resetAutoSlide() {
        clearInterval(this.autoSlide);
        this.startAutoSlide();
      }

      addTouchSupport() {
        let startX = 0;
        this.track.addEventListener('touchstart', e => {
          startX = e.touches[0].clientX;
        });
        this.track.addEventListener('touchend', e => {
          const endX = e.changedTouches[0].clientX;
          const diff = startX - endX;
          if (diff > 50) this.next();
          else if (diff < -50) this.prev();
        });
      }
    }

    // Initialize the Carousel
    document.addEventListener('DOMContentLoaded', () => {
        new Carousel('.carousel-track', '.btn.prev', '.btn.next');
    });
  </script>
</body>
</html>