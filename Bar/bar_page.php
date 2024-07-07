<!DOCTYPE html>
<?php
session_start();
if ($lang_code == "pt") {
    require_once("../lang/lang_pt.php");
} elseif ($lang_code == "fr") {
    require_once("../lang/lang_fr.php");
} else {
    require_once("../lang/lang_en.php");
}

require_once("../Lib/lib.php");
require_once("../Lib/db.php");
require_once("../main/processFormMainPage.php");
$users = getAllUsers();
$is_admin = false;
$is_simpatizante = false;
$is_utilizador = false;

if(isset($_SESSION['id'])) {
    foreach ($users as $user) {
        if ($_SESSION['id'] == $user['utilizador_id']) {
            if ($user['tipo_utilizador'] == 'administrador') {
                $is_admin = true;
            }
            if ($user['tipo_utilizador'] == 'simpatizante') {
                $is_simpatizante = true;
            }
            if ($user['tipo_utilizador'] == 'utilizador') {
                $is_utilizador = true;
            }
            break;
        }
    }
}


include "../main/header_other.php"
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Page</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.css"/>
    <style>
        /* General Page Styling */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        main {
            padding: 20px;
            max-width: 1200px;
            margin: auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .bar-details {
            margin-bottom: 30px;
        }

        .bar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .bar-header h2 {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }

        .bar-photo {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .carousel {
            width: 60%;
        }

        .bar-info-card {
            width: 40%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fafafa;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .bar-info-card p {
            margin: 10px 0;
        }

        .bar-info-card .card-title {
            font-weight: bold;
        }

        .bar-description {
            margin-top: 40px;
        }

        .bar-description h3 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .reviews {
            margin-top: 40px;
        }

        .reviews h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        .review-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .review-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }

        .review-form select {
            width: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .review-form button {
            align-self: flex-start;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background-color: #3E3434;;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .review-form button:hover {
            background-color: #3E3434;;
        }

    
    </style>
</head>
<body>
    <main>
        <div class="bar-details">
            <div class="bar-header">
                <h2 id="bar-name"></h2>
            </div>
            <p id="bar-avg-score"></p>
            <div class="bar-photo">
                <div class="carousel">
                    <!-- Carousel images will be added here dynamically -->
                </div>
                <div class="bar-info-card">
                    <p id="bar-location"></p>
                    <p id="bar-contact"></p>
                    <div id="schedule-list"></div>
                </div>
            </div>
            <div class="bar-description">
                <h3>Descrição</h3>
                <p id="bar-description"></p>
            </div>
        </div>
        <div class="reviews">
            <h3>Reviews</h3>
            <div id="reviews-list"></div>
            <?php if ($is_admin || $is_simpatizante || $is_utilizador): ?>
                <div class="review-form">
                    <textarea id="review-text" placeholder="Write your review here..."></textarea>
                    <select id="classificacao" name="classificacao" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select><br><br>
                    <button id="submit-review">Submit Review</button>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Your Bar Name</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const barId = new URLSearchParams(window.location.search).get('bar_id');
            if (barId) {
                fetch(`../assets/apis/rest/getBarInfo.php?bar_id=${barId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        // Bar details
                        document.getElementById('bar-name').innerText = data.bar.nome;
                        document.getElementById('bar-avg-score').innerText = `<?php echo $lang['avg']; ?> ${data.bar.avg_score}`;

                        // Check if multimedia is available and get the first photo
                        const multimedia = data.multimedia;
                        const carouselContainer = document.querySelector('.carousel');
                        if (multimedia && multimedia.length > 0) {
                            multimedia.forEach(media => {
                                const img = document.createElement('img');
                                img.src = media.url;
                                img.alt = 'Bar Photo';
                                carouselContainer.appendChild(img);
                            });
                        } else {
                            const defaultImg = document.createElement('img');
                            defaultImg.src = 'uploads/default.jpg';
                            defaultImg.alt = 'Default Photo';
                            carouselContainer.appendChild(defaultImg);
                        }

                        // Initialize Slick Carousel
                        $('.carousel').slick({
                            dots: true,
                            arrows: true,
                            infinite: true,
                            speed: 500,
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            autoplay: true,
                            autoplaySpeed: 3000,
                            prevArrow: '<button type="button" class="slick-prev">Previous</button>',
                            nextArrow: '<button type="button" class="slick-next">Next</button>',
                            responsive: [
                                {
                                    breakpoint: 768,
                                    settings: {
                                        arrows: false
                                    }
                                }
                            ]
                        });

                        document.getElementById('bar-description').innerText = data.bar.descricao;
                        document.getElementById('bar-location').innerText = `<?php echo $lang['location']; ?> ${data.bar.localizacao}`;
                        document.getElementById('bar-contact').innerText = `Contact: ${data.bar.contacto}`;

                        // Schedule
                        const scheduleList = document.getElementById('schedule-list');
                        data.schedule.forEach(schedule => {
                            const scheduleElem = document.createElement('p');
                            scheduleElem.innerText = `${schedule.dia_da_semana}: ${schedule.hora_abre} - ${schedule.hora_fecho}`;
                            scheduleList.appendChild(scheduleElem);
                        });

                        // Reviews
                        const reviewsList = document.getElementById('reviews-list');
                        data.reviews.forEach(review => {
                            const reviewElem = document.createElement('p');
                            reviewElem.innerText = `${review.user_name}: ${review.conteudo} (Rating: ${review.classificacao})`;
                            reviewsList.appendChild(reviewElem);
                        });
                    })
                    .catch(error => console.error('Error fetching bar details:', error));
            } else {
                console.error('No bar_id provided in URL');
            }
        });

        document.getElementById('submit-review').addEventListener('click', function() {
            var reviewText = document.getElementById('review-text').value;
            var rating = document.getElementById('classificacao').value;
            var barId = new URLSearchParams(window.location.search).get('bar_id');
            console.log(reviewText);
            console.log(rating);
            console.log(barId);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../assets/apis/rest/submit_review.php', true); // Corrected path
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText); // Show response message
                }
            };
            xhr.send('review_text=' + encodeURIComponent(reviewText) + '&classificacao=' + encodeURIComponent(rating) + '&bar_id=' + encodeURIComponent(barId));
        });
    </script>
</body>
<footer class="footer">
    <p>&copy; 2024 Barometro</p>
</footer>
</html>
                            
