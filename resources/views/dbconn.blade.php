<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

       
    </head>
    <body>
       <div>
        <?php
          try {
            $pdo = DB::connection()->getPdo();
            echo "Connection established successfully.";
          } catch (\Exception $e) {
            var_dump("Could not connect to the database: " . $e->getMessage());
}
       ?> 
       </div>
    
    </body>
    </html>