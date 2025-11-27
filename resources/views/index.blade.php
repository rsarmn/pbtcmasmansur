<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest House KH. Mas Mansyur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

</head>

<body>
    <div class="content" id="mainContent">
        <button class="menu-btn" id="menuBtn">☰</button>
        <div id="location" class="panel">
            <button class="close-btn" onclick="closePanel(event)">✕</button>
            <h2>Our Location</h2>
            <p>We’re located in the heart of the city — just a few minutes from the central station. Come visit us and enjoy the view!</p>
            <div class="map-container">
                <iframe 
                src="{{ $data->maps_link }}" 
                allowfullscreen=""
                loading="lazy">
                </iframe>
            </div>
        </div>
        <div id="contact" class="panel">
            <button class="close-btn" onclick="closePanel(event)">✕</button>
            <h2>Contact Us</h2>
            <p>Reach out to us through the following platforms:</p>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-phone-alt"></i>
                    <div class="text"><a href="https://wa.me/{{ $data->whatsapp }}" target="_blank">{{ $data->whatsapp }} (WhatsApp)</a></div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <div class="text"><a href="mailto:{{ $data->email }}" target="_blank">{{ $data->email }}</a></div>
                </div>
                <div class="contact-item">
                    <i class="fab fa-instagram"></i>
                    <div class="text"><a href="https://instagram.com/{{ $data->instagram }}" target="_blank">{{ '@' . $data->instagram }}</a></div>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div class="text">{{ $data->location }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="sidebar" id="sidebar">
        <button onclick="showPanel('location')">Location</button>
        <button onclick="showPanel('contact')">Contact</button>
    </div>
    <div class="slider">
        <div class="slides">
            <input type="radio" name="radio-btn" id="radio1">
            <input type="radio" name="radio-btn" id="radio2">

            <div class="slide first">
                <img src="{{ asset($data->slider1_image) }}" alt="Slider 1">
                <div class="info">
                    <h1>{!! nl2br(e($data->slider1_text)) !!}</h1>
                </div>
            </div>
            <div class="slide">
                <img src="{{ asset($data->slider2_image) }}" alt="Slider 1">
                <div class="info">
                    <h2>{!! nl2br(e($data->slider2_text)) !!}</h2>
                </div>
            </div>
            <div class="slide">
                <img src="img/LP3.jpg" alt="">
                <div class="info">
                </div>
            </div>
            
            <div class="navigation-auto">
                <div class="auto-btn1"></div>
                <div class="auto-btn2"></div>
            </div>
        </div>
        <div class="navigation-manual">
            <label for="radio1" class="manual-btn"></label>
            <label for="radio2" class="manual-btn"></label>
        </div>
    </div>
    
    <div class="container swiper">
        <h1 class="fade-top animate-hidden">Our Room</h1>
            <div class="availability-check fade-right animate-hidden">
        <p>Cek Ketersediaan Kamar</p>
        <form id="cekForm" action="{{ route('beranda.show') }}" method="GET">
            <label>Check In :</label>
            <input type="date" id="checkin" name="checkin" required>

            <label>Check Out :</label>
            <input type="date" id="checkout" name="checkout" required>

            <label>Jumlah Kamar :</label>
            <input type="number" id="jumlahkamar" name="jumlahkamar" min="1" placeholder="1" required>
            <button type="submit" class="cek-btn">Cek</button>
        </form>
    </div>
        <div class="card-wrapper fade-left animate-hidden">
            <ul class="card-list swiper-wrapper">
                @forelse($roomTypes as $room)
                    @php
                        $fasilitasList = explode(',', $room->fasilitas);
                        $fasilitasPreview = array_slice($fasilitasList, 0, 2);
                    @endphp
                <li class="card-item swiper-slide">
                    <div href="#" class="card-link">
                        @php
                        $nama = $room->jenis_kamar;
                        if ($nama === 'Standard') {
                            $namaFileKhusus = "Standar Room";
                        } else {
                            $namaFileKhusus = $nama;
                        }
                        $jpg  = "images/{$namaFileKhusus}.jpg";
                        $jpeg = "images/{$namaFileKhusus}.jpeg";
                        
                        if (file_exists(public_path($jpg))) {
                            $foto = $jpg;
                        } elseif (file_exists(public_path($jpeg))) {
                            $foto = $jpeg;
                        } else {
                            $foto = "images/default.jpg"; // fallback optional
                        }
                        @endphp
                        <img src="{{ asset($foto) }}" class="card-image">
                        <p class="badge">{{ $room->jenis_kamar }}</p>
                        <h2 class="card-info">
                            <ul>
                                @foreach($fasilitasPreview as $f)
                                    <li>{{ trim($f) }}</li>
                                @endforeach
                            </ul>
                            <strong>Harga: Rp {{ number_format($room->harga, 0, ',', '.') }}</strong>
                            <small class="availability-text" data-room="{{ $room->jenis_kamar }}">
                                @if(isset($room->tersisa))
                                    @if($room->tersisa > 0)
                                        <span style="color: green;">Tersisa {{ $room->tersisa }} kamar</span>
                                    @else
                                        <span style="color: red;">Tidak tersedia</span>
                                    @endif
                                @endif
                            </small>
                        </h2>
                        <div class="card-button">
                            <button class="see-more" data-room="{{ $room->jenis_kamar }}" data-fasilitas="{{ $room->fasilitas }}" data-harga="{{ $room->harga }}">See More</button>
                            <div class="dropdown">
                                <button class="book-now">Book Now ▾</button>
                                <div class="dropdown-content">
                                    <a href="{{ route('booking.corporate', ['kamar' => $room->kamar_id]) }}">Corporate Booking Form</a>
                                    <a href="{{ route('booking.individu', ['kamar' => $room->kamar_id]) }}">Personal Booking Form</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                    @empty
                        <li><p>Tidak ada kamar tersedia untuk tanggal {{ $checkIn }} - {{ $checkOut }}</p></li>
                    @endforelse
            </ul>
            <div class="swiper-pagination"></div>
        </div>
        <div class="swiper-slide-button swiper-button-prev"></div>
        <div class="swiper-slide-button swiper-button-next"></div>
    </div>
    <div id="dropdown-portal"></div>
    <div id="roomPopup" class="popup-overlay">
        <div class="popup-content">
            <span id="closePopup" class="close">&times;</span>
            <h2 id="popupTitle"></h2>
            <ul id="popupFeatures"></ul>
            <div class="dropdown-content" style="display:none">
                <a href="{{ route('booking.corporate', ['kamar' => $room->kamar_id]) }}">Corporate Booking Form</a>
                <a href="{{ route('booking.individu', ['kamar' => $room->kamar_id]) }}">Personal Booking Form</a>
            </div>
            <button class="book-now">Book Now ▾</button>
        </div>
    </div>
    <footer id="mainFooter">
        <div class="footer-container">
            <div class="footer-left">
                <h2>Pesma Inn</h2>
                <h3>KH. Mas Mansur</h3>
            </div>
            <div class="footer-right">
                <div class="footer-top">
                    <a href="https://instagram.com/{{ $data->instagram }}" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/{{ $data->whatsapp }}" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="mailto:{{ $data->email }}" target="_blank" aria-label="Email"><i class="fas fa-envelope"></i></a>
                </div>
                <div class="footer-middle">
                    <p><i class="fas fa-map-marker-alt"></i>{{ $data->location }}</p>
                </div>
                <div class="footer-bottom-links">
                    <a href="#">Home</a>
                    <span>|</span>
                    <a href="https://pesma.ums.ac.id/" target="_blank">Pesma UMS</a>
                </div>
            </div>
        </div>
        <div class="footer-copy">
            <p>&copy; 2025 Pesma Inn KH. Mas Mansur. All rights reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>