<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if (isset($style)) {
        echo "<link rel='stylesheet' href='$style'>";
    }
    echo "<link rel='stylesheet' href='./assets/style/global.css'>";

    ?>
    <link rel="manifest" href="../../manifest.json">
    <script src="assets/js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/localforage/1.10.0/localforage.js"></script>
    <title><?= htmlspecialchars($title) ?></title>
</head>

<body>
    <div id="menu">
        <form action="index.php" method="GET">
            <button type="button" class="button" id="closeMenu">
            <span></span>
            <svg width="50px" height="50px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.99486 7.00636C6.60433 7.39689 6.60433 8.03005 6.99486 8.42058L10.58 12.0057L6.99486 15.5909C6.60433 15.9814 6.60433 16.6146 6.99486 17.0051C7.38538 17.3956 8.01855 17.3956 8.40907 17.0051L11.9942 13.4199L15.5794 17.0051C15.9699 17.3956 16.6031 17.3956 16.9936 17.0051C17.3841 16.6146 17.3841 15.9814 16.9936 15.5909L13.4084 12.0057L16.9936 8.42059C17.3841 8.03007 17.3841 7.3969 16.9936 7.00638C16.603 6.61585 15.9699 6.61585 15.5794 7.00638L11.9942 10.5915L8.40907 7.00636C8.01855 6.61584 7.38538 6.61584 6.99486 7.00636Z" fill="#ffffffff"/>
            </svg>
        </button>
            <div class="input">
                <span></span>
                <img src="./assets/images/loupe.svg" alt="Search Icon">
                <input type="text" name="contenu" placeholder="Rechercher des créations..." value="<?php echo isset($_GET['contenu']) ? htmlspecialchars($_GET['contenu']) : ''; ?>" />
                <input type="hidden" name="page" value="catalogue">
                <input type="hidden" name="searchAuthor" value="<?php echo isset($_GET['searchAuthor']) ? $_GET['searchAuthor'] : '' ?>">
                <input type="hidden" name="categorie" value="<?php echo isset($_GET['categorie']) ? $_GET['categorie'] : '' ?>">
                <input type="hidden" name="tri" value="<?php echo isset($_GET['tri']) ? $_GET['tri'] : '' ?>">
                <input type="hidden" name="genre" value="<?php echo isset($_GET['genre']) ? $_GET['genre'] : '' ?>">
            </div>
        </form>
        <div>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            ?>
            <?php if (!isset($_SESSION['id'])): ?>
                <button class="button" data-action="register">
                    <span></span>
                    <p>S'inscrire</p>
                </button>
                <button class="button" data-action="login">
                    <span></span>
                    <p>Connexion</p>
                </button>
            <?php else: ?>
                <form id="goNotif" action="?page=notification" method="post">
                    <input type="hidden" name="account" value="<?= htmlspecialchars($_SESSION['id']) ?>">
                </form>
                <button title="Votre streak actuelle est de : <?= $_SESSION['streak'] ?> &#10;Votre streak la plus haute est de : <?= $_SESSION['highestStreak'] ?> " class="button btn-streak" data-action="streak">
                    <span></span>
                    <svg class="icon-streak" viewBox="0 0 60 60" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <style type="text/css">
                            .st0 {
                                fill: #B4E6DD;
                            }

                            .st1 {
                                fill: #80D4C4;
                            }

                            .st2 {
                                fill: #D2F0EA;
                            }

                            .st3 {
                                fill: #FFFFFF;
                            }

                            .st4 {
                                fill: #FBD872;
                            }

                            .st5 {
                                fill: #DB7767;
                            }

                            .st6 {
                                fill: #F38E7A;
                            }

                            .st7 {
                                fill: #F6AF62;
                            }

                            .st8 {
                                fill: #32A48E;
                            }

                            .st9 {
                                fill: #A38FD8;
                            }

                            .st10 {
                                fill: #7C64BD;
                            }

                            .st11 {
                                fill: #EAA157;
                            }

                            .st12 {
                                fill: #9681CF;
                            }

                            .st13 {
                                fill: #F9C46A;
                            }

                            .st14 {
                                fill: #CE6B61;
                            }
                        </style>
                        <g>
                            <path class="st7" d="M21.04,20.63c0,0-1.79,8.99-0.96,11.47c0,0,1.1-13.6,11.67-24.1c0,0-0.34,8.79,5.15,13.25s6.73,11.4,6.73,11.4   s0.62-4.74-1.24-12.33c0,0,11.67,12.12,6.32,25.65c0,0-6.45,14.9-24.92,8.38S17.74,22.97,21.04,20.63z" />
                            <path class="st4" d="M40.75,32.12c0.17,3.81-1.55,4.81-2.94,5.01c-0.89,0.13-1.79-0.1-2.57-0.53c-7.65-4.15-3.72-14.29-3.72-14.29   c-5.01,4.79-2.4,14.74-2.4,14.74s1.55,5.64-1.62,6.13c-1.59,0.25-2.33-0.39-2.67-1.08c-0.31-0.64-0.31-1.39-0.04-2.05   c2.15-5.34,0.31-8.36,0.31-8.36c-1.03,3.06-2.12,5.08-2.91,6.29c-1.12,1.72-1.83,3.68-1.93,5.73C19.9,51.35,26.3,54.4,26.3,54.4   c10.51,5.36,15.23-4.44,15.23-4.44C46.96,39.52,40.75,32.12,40.75,32.12z" />
                        </g>
                    </svg>
                    <p class="streak-text"> <?= $_SESSION['streak'] ?> </p>
                </button>
                <button class="button notif-btn" data-action="notification"
                    onclick="document.getElementById('goNotif').submit()">
                    <span></span>
                    <p>Notification</p>
                    <div class="notif-dot"></div>
                </button>
                <script src="./assets/js/bellScript.js"></script>
                <button class="button" data-action="create" onclick="window.location.href='?page=createContent'">
                    <span></span>
                    <p>Création</p>
                </button>
                <button class="button" data-action="account">
                    <span></span>
                    <p>Mon compte</p>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <header>
        <button class="button" id="openMenu">
            <span></span>
            <svg width="35px" height="35px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 18L20 18" stroke="#ffffffff" stroke-width="2" stroke-linecap="round"/>
                <path d="M4 12L20 12" stroke="#ffffffff" stroke-width="2" stroke-linecap="round"/>
                <path d="M4 6L20 6" stroke="#ffffffff" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </button>
        <picture onclick="window.location.href='?page=home'" alt="Logo">
            <source srcset="./assets/images/logo.svg" media="(width >= 1480px)">
            <img src="./assets/images/icon.png">
        </picture>
        <form action="index.php" method="GET">
            <div class="input">
                <span></span>
                <img src="./assets/images/loupe.svg" alt="Search Icon">
                <input type="text" name="contenu" placeholder="Rechercher des créations..." value="<?php echo isset($_GET['contenu']) ? htmlspecialchars($_GET['contenu']) : ''; ?>" />
                <input type="hidden" name="page" value="catalogue">
                <input type="hidden" name="searchAuthor" value="<?php echo isset($_GET['searchAuthor']) ? $_GET['searchAuthor'] : '' ?>">
                <input type="hidden" name="categorie" value="<?php echo isset($_GET['categorie']) ? $_GET['categorie'] : '' ?>">
                <input type="hidden" name="tri" value="<?php echo isset($_GET['tri']) ? $_GET['tri'] : '' ?>">
                <input type="hidden" name="genre" value="<?php echo isset($_GET['genre']) ? $_GET['genre'] : '' ?>">
            </div>
        </form>
        <div>
            <?php
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            ?>
            <?php if (!isset($_SESSION['id'])): ?>
                <button class="button" data-action="register">
                    <span></span>
                    <p>S'inscrire</p>
                </button>
                <button class="button" data-action="login">
                    <span></span>
                    <p>Connexion</p>
                </button>
            <?php else: ?>
                <button title="Votre streak actuelle est de : <?= $_SESSION['streak'] ?> &#10;Votre streak la plus haute est de : <?= $_SESSION['highestStreak'] ?> " class="button btn-streak" data-action="streak">
                    <span></span>
                    <svg class="icon-streak" viewBox="0 0 60 60" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <style type="text/css">
                            .st0 {
                                fill: #B4E6DD;
                            }

                            .st1 {
                                fill: #80D4C4;
                            }

                            .st2 {
                                fill: #D2F0EA;
                            }

                            .st3 {
                                fill: #FFFFFF;
                            }

                            .st4 {
                                fill: #FBD872;
                            }

                            .st5 {
                                fill: #DB7767;
                            }

                            .st6 {
                                fill: #F38E7A;
                            }

                            .st7 {
                                fill: #F6AF62;
                            }

                            .st8 {
                                fill: #32A48E;
                            }

                            .st9 {
                                fill: #A38FD8;
                            }

                            .st10 {
                                fill: #7C64BD;
                            }

                            .st11 {
                                fill: #EAA157;
                            }

                            .st12 {
                                fill: #9681CF;
                            }

                            .st13 {
                                fill: #F9C46A;
                            }

                            .st14 {
                                fill: #CE6B61;
                            }
                        </style>
                        <g>
                            <path class="st7" d="M21.04,20.63c0,0-1.79,8.99-0.96,11.47c0,0,1.1-13.6,11.67-24.1c0,0-0.34,8.79,5.15,13.25s6.73,11.4,6.73,11.4   s0.62-4.74-1.24-12.33c0,0,11.67,12.12,6.32,25.65c0,0-6.45,14.9-24.92,8.38S17.74,22.97,21.04,20.63z" />
                            <path class="st4" d="M40.75,32.12c0.17,3.81-1.55,4.81-2.94,5.01c-0.89,0.13-1.79-0.1-2.57-0.53c-7.65-4.15-3.72-14.29-3.72-14.29   c-5.01,4.79-2.4,14.74-2.4,14.74s1.55,5.64-1.62,6.13c-1.59,0.25-2.33-0.39-2.67-1.08c-0.31-0.64-0.31-1.39-0.04-2.05   c2.15-5.34,0.31-8.36,0.31-8.36c-1.03,3.06-2.12,5.08-2.91,6.29c-1.12,1.72-1.83,3.68-1.93,5.73C19.9,51.35,26.3,54.4,26.3,54.4   c10.51,5.36,15.23-4.44,15.23-4.44C46.96,39.52,40.75,32.12,40.75,32.12z" />
                        </g>
                    </svg>
                    <p class="streak-text"> <?= $_SESSION['streak'] ?> </p>
                </button>
                <form id="goNotif" action="?page=notification" method="post">
                    <input type="hidden" name="account" value="<?= htmlspecialchars($_SESSION['id']) ?>">
                </form>
                <button class="button notif-btn btn-notif" data-action="notification"
                    onclick="document.getElementById('goNotif').submit()">
                    <span></span>
                    <svg class="icon-notif" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                        <path fill="#ffffff" d="M320 64C306.7 64 296 74.7 296 88L296 97.7C214.6 109.3 152 179.4 152 264L152 278.5C152 316.2 142 353.2 123 385.8L101.1 423.2C97.8 429 96 435.5 96 442.2C96 463.1 112.9 480 133.8 480L506.2 480C527.1 480 544 463.1 544 442.2C544 435.5 542.2 428.9 538.9 423.2L517 385.7C498 353.1 488 316.1 488 278.4L488 263.9C488 179.3 425.4 109.2 344 97.6L344 87.9C344 74.6 333.3 63.9 320 63.9zM488.4 432L151.5 432L164.4 409.9C187.7 370 200 324.6 200 278.5L200 264C200 197.7 253.7 144 320 144C386.3 144 440 197.7 440 264L440 278.5C440 324.7 452.3 370 475.5 409.9L488.4 432zM252.1 528C262 556 288.7 576 320 576C351.3 576 378 556 387.9 528L252.1 528z" />
                    </svg>
                    <div class="notif-dot"></div>
                </button>
                <script src="./assets/js/bellScript.js"></script>
                <button class="button btn-create" data-action="create" onclick="window.location.href='?page=createContent'">
                    <span></span>
                    <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 12H18M12 6V18" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button class="button btn-account" data-action="account">
                    <span></span>
                    <svg class="icon-account" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M35 0C15.6702 0 0 15.6702 0 35C0 54.3298 15.6702 70 35 70C54.3298 70 70 54.3298 70 35C70 15.6702 54.3298 0 35 0ZM4.88372 35C4.88401 30.1471 6.05705 25.3661 8.30295 21.0641C10.5489 16.7622 13.8011 13.0667 17.7828 10.2923C21.7645 7.51795 26.3576 5.74688 31.1712 5.1299C35.9847 4.51292 40.8761 5.06832 45.4287 6.7488C49.9814 8.42928 54.0605 11.1851 57.3188 14.7815C60.5771 18.378 62.918 22.7085 64.1423 27.4045C65.3665 32.1005 65.4378 37.0227 64.35 41.7522C63.2622 46.4816 61.0477 50.8782 57.8949 54.5674C57.1875 52.081 55.8793 49.8067 54.0856 47.9451C51.5395 45.2981 48.3553 43.7581 45.5944 42.8986C43.5563 42.2637 41.6353 42.9963 40.3721 43.8526C39.2 44.6405 37.3116 45.5814 35 45.5814C32.6884 45.5814 30.8 44.6372 29.6279 43.8526C28.3647 42.9995 26.4437 42.2637 24.4056 42.8986C21.6414 43.7581 18.4605 45.2981 15.9144 47.9451C14.1211 49.8058 12.813 52.0789 12.1051 54.5642C7.43222 49.1183 4.86967 42.1759 4.88372 35ZM16.3214 58.6242C16.5688 55.3326 17.8191 53.0112 19.4372 51.3279C21.2377 49.4526 23.6047 48.2642 25.8577 47.5609C25.9684 47.5348 26.084 47.5382 26.193 47.5707C26.4445 47.6393 26.6824 47.7505 26.8963 47.8995C28.5274 49 31.3698 50.4651 35 50.4651C38.6302 50.4651 41.4726 49 43.1037 47.8995C43.3178 47.7517 43.5557 47.6415 43.807 47.574C43.9157 47.5404 44.0313 47.5359 44.1423 47.5609C46.3953 48.2642 48.7623 49.4526 50.5628 51.3279C52.1809 53.0112 53.4279 55.3326 53.6786 58.6242C48.3668 62.8403 41.7816 65.1291 35 65.1163C28.2184 65.1291 21.6332 62.8403 16.3214 58.6242ZM27.6744 28.4884C27.6744 24.4186 30.8326 21.1628 35 21.1628C39.1674 21.1628 42.3256 24.4186 42.3256 28.4884C42.3256 32.5581 39.1674 35.814 35 35.814C30.8326 35.814 27.6744 32.5581 27.6744 28.4884ZM35 16.2791C33.393 16.2661 31.7995 16.573 30.3123 17.1819C28.8252 17.7909 27.4741 18.6897 26.3377 19.8261C25.2014 20.9624 24.3025 22.3135 23.6936 23.8007C23.0846 25.2879 22.7777 26.8814 22.7907 28.4884C22.7777 30.0953 23.0846 31.6888 23.6936 33.176C24.3025 34.6632 25.2014 36.0143 26.3377 37.1507C27.4741 38.287 28.8252 39.1858 30.3123 39.7948C31.7995 40.4038 33.393 40.7107 35 40.6977C36.607 40.7107 38.2005 40.4038 39.6876 39.7948C41.1748 39.1858 42.5259 38.287 43.6623 37.1507C44.7986 36.0143 45.6975 34.6632 46.3064 33.176C46.9154 31.6888 47.2223 30.0953 47.2093 28.4884C47.2223 26.8814 46.9154 25.2879 46.3064 23.8007C45.6975 22.3135 44.7986 20.9624 43.6623 19.8261C42.5259 18.6897 41.1748 17.7909 39.6876 17.1819C38.2005 16.573 36.607 16.2661 35 16.2791Z" fill="white" />
                    </svg>
                </button>
            <?php endif; ?>
        </div>
        <div class="modal-streak" id="showStreakModal">
            <div class="modal">

                <button type="button" class="closeModal"><span>&times;</span></button>

                <div class="title">
                    <h2 id="hStreak"><svg width="70" height="70" viewBox="0 0 60 60" id="Layer_1" version="1.1" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                            <style type="text/css">
                                .st0 {
                                    fill: #B4E6DD;
                                }

                                .st1 {
                                    fill: #80D4C4;
                                }

                                .st2 {
                                    fill: #D2F0EA;
                                }

                                .st3 {
                                    fill: #FFFFFF;
                                }

                                .st4 {
                                    fill: #FBD872;
                                }

                                .st5 {
                                    fill: #DB7767;
                                }

                                .st6 {
                                    fill: #F38E7A;
                                }

                                .st7 {
                                    fill: #F6AF62;
                                }

                                .st8 {
                                    fill: #32A48E;
                                }

                                .st9 {
                                    fill: #A38FD8;
                                }

                                .st10 {
                                    fill: #7C64BD;
                                }

                                .st11 {
                                    fill: #EAA157;
                                }

                                .st12 {
                                    fill: #9681CF;
                                }

                                .st13 {
                                    fill: #F9C46A;
                                }

                                .st14 {
                                    fill: #CE6B61;
                                }
                            </style>
                            <g>
                                <path class="st7" d="M21.04,20.63c0,0-1.79,8.99-0.96,11.47c0,0,1.1-13.6,11.67-24.1c0,0-0.34,8.79,5.15,13.25s6.73,11.4,6.73,11.4   s0.62-4.74-1.24-12.33c0,0,11.67,12.12,6.32,25.65c0,0-6.45,14.9-24.92,8.38S17.74,22.97,21.04,20.63z" />
                                <path class="st4" d="M40.75,32.12c0.17,3.81-1.55,4.81-2.94,5.01c-0.89,0.13-1.79-0.1-2.57-0.53c-7.65-4.15-3.72-14.29-3.72-14.29   c-5.01,4.79-2.4,14.74-2.4,14.74s1.55,5.64-1.62,6.13c-1.59,0.25-2.33-0.39-2.67-1.08c-0.31-0.64-0.31-1.39-0.04-2.05   c2.15-5.34,0.31-8.36,0.31-8.36c-1.03,3.06-2.12,5.08-2.91,6.29c-1.12,1.72-1.83,3.68-1.93,5.73C19.9,51.35,26.3,54.4,26.3,54.4   c10.51,5.36,15.23-4.44,15.23-4.44C46.96,39.52,40.75,32.12,40.75,32.12z" />
                            </g>
                        </svg>Vos streak(s)</h2>
                </div>

                <div class="streakContent">
                    <p>Vous avez joué <strong><?= $_SESSION['streak'] ?></strong> jour(s) consécutif(s) </p>
                    <p>Votre record <strong><?= $_SESSION['highestStreak'] ?></strong> jour(s) consécutif(s) </p>
                </div>

            </div>
        </div>
    </header>
    <!-- Création des listeners des boutons du header -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('header .button, #menu .button').forEach(btn => {
                const action = btn.dataset.action;

                if (!action) return;

                btn.addEventListener('click', () => {
                    switch (action) {
                        case 'register':
                            window.location.href = '?page=log&typelog=register';
                            break;
                        case 'login':
                            window.location.href = '?page=log&typelog=connection';
                            break;
                        case 'account':
                            window.location.href = '?page=profil';
                            break;
                        case 'streak':
                            openModal();
                            break;
                    }
                });
            });
        });

        function openModal() {
            const modal = document.getElementById("showStreakModal");
            modal.style.display = "flex";
        }

        const closeM = document.querySelector(".closeModal");

        closeM.addEventListener("click", () => closeModal());

        function closeModal() {
            const modal = document.getElementById("showStreakModal");
            modal.style.display = "none";
        }
    </script>