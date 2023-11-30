<?php

unset($argv[0]);

$command = $argv[1];
$folderName = $argv[2];
$fileName = $argv[3];

if(empty($folderName)) {
    die('É necessário informar o nome da pasta.');
}

if(empty($fileName)) {
    die('É necessário informar o nome do arquivo.');
}

switch($command) {
    case 'api_repository' :
        createApiRepository($folderName, $fileName);
        break;
    case 'api_service' : 
        createApiService($folderName, $fileName);
        break;
    case 'api_controller': 
        createApiController($folderName, $fileName);
        break;
    case 'api_endpoint':
        createApiRepository($folderName, $fileName);
        echo "Repository '".$fileName."' criado.\n";
        createApiService($folderName, $fileName);
        echo "Service '".$fileName."' criado.\n";
        createApiController($folderName, $fileName);
        echo "Controller '".$fileName."' criado.\n";
}

function createApiRepository(string $folder, string $file) {

    $folderPath = "./app/src/Repositories/{$folder}";

    if(!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    $filePath = "./app/src/Repositories/{$folder}/{$file}Repository.php";
    
    $content = "<?php\n\n";
    $content .= "namespace App\Repositories\\" . $folder . ";\n\n";
    $content .= "use App\Db\Connections\Connection;\n \n";
    $content .= "class {$file}Repository {\n\n";
    $content .= 'private Connection $connection;' . "\n \n";
    $content .= '    public function __construct(Connection $connection) {' . "\n";
    $content .= '        $this->connection = $connection;' . "\n";
    $content .= "    }\n\n";
    $content .= '    public function getConnection() : Connection {' . "\n";
    $content .= '        return $this->connection;' . "\n";
    $content .= "    }\n";
    $content .= "}\n";

    file_put_contents($filePath, $content);
}

function createApiService(string $folder, string $file) {

    $folderPath = "./app/src/Services/{$folder}";

    if(!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    $filePath = "./app/src/Services/{$folder}/{$file}Service.php";
    
    $content = "<?php \n\n";
    $content .= "namespace App\Services\\" . $folder . ";\n\n";
    $content .= 'use App\Repositories\\' . $folder . '\\' . $file . 'Repository;' . "\n\n";
    $content .= "class {$file}Service {\n\n";
    $content .= '    private '.$file.'Repository $repository;' . "\n\n";
    $content .= '    public function __construct('.$file.'Repository $repository) {' . "\n";
    $content .= '        $this->repository = $repository;' . "\n";
    $content .= "    }\n\n";
    $content .= '    public function getRepository() : '.$file.'Repository  {' . "\n";
    $content .= '        return $this->repository;' . "\n";
    $content .= "    }\n";
    $content .= "}\n";

    file_put_contents($filePath, $content);
}

function createApiController($folder, $file) {

    $folderPath = "./app/src/Controllers/{$folder}";

    if(!is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }
    

    $filePath = "./app/src/Controllers/{$folder}/{$file}Controller.php";
    
    $content = "<?php \n\n";
    $content .= "namespace App\Controllers\\" . $folder . ";\n\n";
    $content .= "use App\Controllers\Controller;\n";
    $content .= 'use App\Services\\'.$folder. '\\' .$file. "Service;\n\n";
    $content .= "class {$file}Controller extends Controller {\n\n";
    $content .= '    public static function getService() : '.$file.'Service  {' ."\n";
    $content .= "        return self::get('App\\Services\\" . $folder . "\\" . $file . "Service');" . "\n";


    $content .= "    }\n";
    $content .= "}\n";

    file_put_contents($filePath, $content);

}