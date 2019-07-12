<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fake payment gateway</title>
</head>
<body>
    <h1>Do you want to pay {{ $data->amount }} {{ $systemCurrencyCode }}</h1>
    <form action="" method="post">
        <button style="padding: 5px 12px;" type="submit" name="decision" value="yes">Yes</button>
        <button style="padding: 5px 12px;" type="submit" name="decision" value="no">No</button>
    </form>
</body>
</html>