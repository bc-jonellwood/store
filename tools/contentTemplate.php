<?php
// Created: 2025/03/14 08:17:56
// Last Modified: 2025/03/26 15:26:13
// There is a VS Code extension called 'Auto Time Stamp' that will automatically add the created and last modified comments for you. If you don't want this in the file you can remove it from /tools/contentTemplate.php  
?>
<h1>About This Template</h1>
<p>Welcome to this Template! We've been a template script since <?= $foundingYear ?>.</p>
<div class="team-members">
    <h1>Meet the Team</h1>
    <?php foreach ($teamMembers as $member): ?>
        <div class="team-member">
            <h3><?= htmlspecialchars($member['name']) ?></h3>
            <p><?= htmlspecialchars($member['position']) ?></p>
        </div>
    <?php endforeach; ?>
</div>