<?php
require_once "config/langconfig.php";
// Start or resume the current session
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

?>
<header class="header">
    <div class="header__container">
        <a href="https://www.rewe-digital.com" target="_blank" rel="noreferrer noopener" title="REWE Digital GmbH">
            <img src="assets/img/logo.png" alt="REWE Digital GmbH" class="header__logo">
        </a>
        <nav class="nav">
            <ul class="nav__menu">
                <a href="index.php">
                    <li class="menu__item no__sub">
                        <?php echo $words['navigation']['home'] ." (". $_SESSION['dbEnvironment'].")"; ?>
                    </li>
                </a>
                <a>
                    <li class="menu__item">
                        <?php echo $words['navigation']['employee']; ?>
                        <ul class="sub__menu">
                            <a href="create-employee.php">
                                <li><?php echo $words['navigation']['create_employee']; ?></li>
                            </a>
                            <a href="remove-employee.php">
                                <li><?php echo $words['navigation']['remove_employee']; ?></li>
                            </a>
                        </ul>
                    </li>
                </a>
                <a>
                    <li class="menu__item">
                        <?php echo $words['navigation']['shifts']; ?>
                        <ul class="sub__menu">
                            <a href="create-shift.php">
                                <li><?php echo $words['navigation']['create_shifts']; ?></li>
                            </a>
                            <a href="remove-shift.php">
                                <li><?php echo $words['navigation']['remove_shifts']; ?></li>
                            </a>
                        </ul>
                    </li>
                </a>
                <a>
                    <li class="menu__item">
                        <?php echo $words['navigation']['database']; ?>
                        <ul class="sub__menu">
                            <a href="backup-database.php">
                                <li><?php echo $words['navigation']['backup_database']; ?></li>
                            </a>
                            <a href="health-database.php">
                                <li><?php echo $words['navigation']['health_database']; ?></li>
                            </a>
                        </ul>
                    </li>
                </a>
                <a>
                    <li class="menu__item">
                        <?php
                        $currentLang = $_SESSION['lang'] ?? 'en';
                        $flags = [
                            'en' => [
                                'src' => './assets/img/uk.png',
                            ],
                            'es' => [
                                'src' => './assets/img/spain.png',
                            ],
                            'de' => [
                                'src' => './assets/img/germany.png',
                            ]
                        ];

                        if (array_key_exists($currentLang, $flags)) {
                            echo '<img src="' . $flags[$currentLang]['src'] . '" style="float: left; height: 20px; user-select: none; max-width: 50px; max-height: 50px; vertical-align: middle; margin-right: 10px;">';
                        }
                        ?>
                        <?php echo $words['navigation']['language']; ?>
                        <ul class="sub__menu">
                            <a href="lang.php?l=en">
                                <li>
                                    <img src="./assets/img/uk.png" alt="English" style="float: left; height: 20px; user-select: none; max-width: 50px; max-height: 50px; vertical-align: middle; text-align: middle; margin-right: 10px;">
                                    <?php echo $words['navigation']['english']; ?>
                                </li>
                            </a>
                            <a href="lang.php?l=es">
                                <li>
                                    <img src="./assets/img/spain.png" alt="Español" style="float: left; height: 20px; user-select: none; max-width: 50px; max-height: 50px; vertical-align: middle; text-align: middle; margin-right: 10px;">
                                    <?php echo $words['navigation']['spanish']; ?>
                                </li>
                            </a>
                            <a href="lang.php?l=de">
                                <li>
                                    <img src="./assets/img/germany.png" alt="Deutsch" style="float: left; height: 20px; user-select: none; max-width: 50px; max-height: 50px; vertical-align: middle; text-align: middle; margin-right: 10px;">
                                    <?php echo $words['navigation']['german']; ?>
                                </li>
                            </a>
                        </ul>
                    </li>
                </a>
            </ul>
        </nav>
    </div>
</header>