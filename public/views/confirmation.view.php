<?php require('partials/head.php') ?>
<?php require('partials/nav.php') ?>
<?php require('partials/banner.php') ?>

<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <p class="pb-2 underline decoration-solid decoration-blue-600">Authorisation successful</p>
        <span class="font-bold <?= $result ? 'text-green-600' : 'text-red-600' ?>"><?= $result ? 'OK' : 'NOK' ?></span>
        <p class="py-2 underline decoration-solid decoration-blue-600">Access Token</p>
        <span class="font-bold"><?= $result->access_token ?></span>
        <p class="py-2 underline decoration-solid decoration-blue-600">Refresh Token</p>
        <span class="font-bold"><?= $result->refresh_token ?></span>
    </div>
</main>

<?php require('partials/footer.php') ?>
