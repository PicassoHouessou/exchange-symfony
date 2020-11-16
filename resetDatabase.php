<?php
    echo "Commence la suppression de la base de donné \n ";
    exec("php bin/console doctrine:database:drop --force");
    exec("php bin/console doctrine:database:create ");
    exec("php bin/console doctrine:schema:create");
    echo "Tout c'est bien passe \n ";
