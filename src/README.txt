ЛОГИ:
Если в классе используются исключения и требуется отдельный файл с логами для этого класса, тогда в конструкторе требуется
создать публичное статическое свойство $logger и поместить туда объект класса App\Logger, в параметры конструктора передать путь до файла.

Если свойства $logger не будет то сообщения из исключений будут записываться в дефолтный файл с логами /upload/Logs/generalLog.txt

ИСКЛЮЧЕНИЯ:
Если исключение не перехватывается блоком catch то оно перехватится в /local/php_interface/init.php. Текст исключения запишется в лог.