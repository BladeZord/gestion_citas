<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class CustomDailyLogFormatter
{
    /**
     * Personalizar el handler de logs diarios con formato personalizado
     * Formato: Sol_agendamiento_cita-{dd-MM-yyyy}.log
     * Nota: Usamos guiones en lugar de barras para compatibilidad con sistemas de archivos
     */
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof RotatingFileHandler) {
                // Obtener propiedades del handler original usando reflexión
                $reflection = new \ReflectionClass($handler);
                $maxFiles = $reflection->getProperty('maxFiles');
                $maxFiles->setAccessible(true);
                $maxFilesValue = $maxFiles->getValue($handler);
                
                // Crear un handler personalizado que use el formato dd-MM-yyyy
                $customHandler = new class(
                    storage_path('logs/Sol_agendamiento_cita.log'),
                    $maxFilesValue,
                    $handler->getLevel(),
                    $handler->getBubble()
                ) extends RotatingFileHandler {
                    protected function getTimedFilename(): string
                    {
                        $fileInfo = pathinfo($this->filename);
                        // Formato: dd-MM-yyyy (usando guiones para compatibilidad)
                        $date = date('d-m-Y', $this->mustRotate ? $this->nextRotation : time());
                        $timedFilename = 'Sol_agendamiento_cita-' . $date;
                        
                        return $fileInfo['directory'] . DIRECTORY_SEPARATOR . $timedFilename . '.' . $fileInfo['extension'];
                    }
                };
                
                // Reemplazar el handler original
                $logger->popHandler();
                $logger->pushHandler($customHandler);
            }
        }
    }
}
