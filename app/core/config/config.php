<?php
return [
  'db' => [
    'host' => 'mysql219.phy.lolipop.lan',
    'user' => 'LAA1517347',
    'password' => 'babymotsu',
    'dbname' => 'LAA1517347-baby',
    'options' => [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  ],
];
