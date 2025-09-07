Se solicita inicializar un proyecto para un aplicativo WEB.

Para esto se da la instrucción de utilizar una versión de laravel que permita desplegar sin necesidad de utilizar la terminal ya que el plan de hotinger que se tiene es uno que no tiene acceso a VPS.

Se debe conciderar la intención que se tiene para desplegar el proyecto:
queremos que el proyecto se trabaje de manera local y se empaquete lo necesario para que funsione, installando todas las dependencias requeridas para su buen funsionamiento, ya que se busca que solo sea comprimir el proyecto y trasladarlo al hostinger para luego descomprimirlo, ajustar rutas y que todo quede funsionando a la perfección ademas de que sea bien optimizado y no haya errores de cache o sobrecarga de este mismo.

se requiere hacer un login con 4 usuarios:
1. Auditado
2. Auditor
3. Jefe Auditor
4. Super Administrador

estos usuarios tendras permisos distintos.
para esta etapa solo se busque que se pueden identificar en la base de datos y en el backend esto relacionado a que se le de una interfaz o opciones distintas, pero desde el bacekend ya que en las vistas frontend lo unico que va a cambiar es que va hacer redireccionado a un apartado que corresponda a su rol. es decir si ingresa un super administrador pues debera la pagina redirigirlo a una interfaz distinta y especial por su rol, esto igual para los otros roles.

Nota: no quiero que despues de ingresar sesión haya un interfaz para todos los roles, lo que quiero es que lo lleves a un apartado unico de ese rol, para inicial esto vamos a crear modulos independientes entreroles porque tampoco busque que la interfaz de inicio sea la misma para todos los roles y que lo unico que cambie sea el nombre del rol en la bienvenida al usuario (esto quiere decir que no puedes crear una interfaz ejemplo dasboard.php y qeu en esta haya un backend que muestre el rol que corresponda al usuario logueado, sino que debes literal hacer otra innterfaz puede ser con el mismo nombre dashboard.php pero desde otro directorio).
