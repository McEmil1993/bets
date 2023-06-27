<?php $url_path = $_SERVER['REQUEST_URI'] ?> 

<div class="dividend-tabs">
    
    <a href="/logpage/dividend_log_match_info_prematch.php" class="tab <?php echo strpos($url_path, 'dividend_log_match_info_prematch') !== false ? 'selected': '' ?>">
        Match Info (Prematch)
    </a>
    <a href="/logpage/dividend_log_match_info_realtime.php" class="tab <?php echo strpos($url_path, 'dividend_log_match_info_realtime') !== false ? 'selected': '' ?>">
        Match Info (Realtime)
    </a>
    <a href="/logpage/dividend_log_odds_info_prematch.php" class="tab <?php echo strpos($url_path, 'dividend_log_odds_info_prematch') !== false ? 'selected': '' ?>">
        Odds Info (Prematch)
    </a>
    <a href="/logpage/dividend_log_odds_info_realtime.php" class="tab <?php echo strpos($url_path, 'dividend_log_odds_info_realtime') !== false ? 'selected': '' ?>">
        Odds Info (Realtime)
    </a>
    <a href="/logpage/dividend_log_score_info_prematch.php" class="tab <?php echo strpos($url_path, 'dividend_log_score_info_prematch') !== false ? 'selected': '' ?>">
        Score Info (Prematch)
    </a>
    <a href="/logpage/dividend_log_score_info_realtime.php" class="tab <?php echo strpos($url_path, 'dividend_log_score_info_realtime') !== false ? 'selected': '' ?>">
        Score Info (Realtime)
    </a>
</div>