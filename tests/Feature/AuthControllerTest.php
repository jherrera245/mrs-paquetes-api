<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Clientes;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailVerificationNotification;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Seeders o creación manual de datos necesarios para las pruebas
        $this->seed(); // Esto sigue siendo útil para asegurarte de que los roles y permisos estén presentes.
        Notification::fake(); // Simular las notificaciones en lugar de enviar correos electrónicos reales.
    }

    /**
     * Prueba para verificar que un administrador puede registrar un cliente con datos válidos.
     */
    public function test_admin_puede_registrar_cliente_con_datos_validos()
    {
        // Crear el rol 'Client' que se asignará al usuario
        $role = Role::firstOrCreate(['name' => 'Client']);

        // Datos que se enviarán en la solicitud de registro
        $data = [
            'email' => 'sr4787814@gmail.com',
            'password' => 'password123',
            'nombre' => 'John',
            'apellido' => 'Doe',
            'nombre_comercial' => 'Doe Inc.',
            'dui' => '01234567-8',
            'telefono' => '1234-5678',
            'id_tipo_persona' => 1,
            'es_contribuyente' => true,
            'id_estado' => 1,
            'id_departamento' => 1,
            'id_municipio' => 1,
            'nit' => '1234-567890-123-4',
            'nrc' => '123456-7',
            'giro' => 'Comercio',
            'nombre_empresa' => 'Doe Enterprises',
            'direccion' => '123 Main St',
        ];

        // Enviar la solicitud POST a la ruta de registro de administrador
        $response = $this->postJson('/api/admin/register', $data);

        // Verificar que la respuesta sea 200 OK
        $response->assertStatus(200);

        // Verificar que los datos del usuario y cliente se hayan insertado correctamente en la base de datos
        $this->assertDatabaseHas('users', ['email' => 'sr4787814@gmail.com']);
        $this->assertDatabaseHas('clientes', ['nombre' => 'John']);

        // Verificar que se haya enviado la notificación de verificación de correo electrónico
        Notification::assertSentTo(User::where('email', 'sr4787814@gmail.com')->first(), EmailVerificationNotification::class);
    }

    /**
     * Prueba para verificar que el registro de un cliente falla con datos inválidos.
     */
    public function test_falla_registro_de_cliente_con_datos_invalidos()
    {
        $data = [
            'email' => 'correo-invalido',
            'password' => 'short',
        ];

        // Realizar la solicitud con datos inválidos
        $response = $this->postJson('/api/admin/register', $data);

        // Verificar que la respuesta indique errores de validación
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Prueba para verificar que un usuario estándar puede registrarse correctamente.
     */
    public function test_usuario_puede_registrarse()
    {
        // Generar un email único para la prueba
        $email = 'test_' . $this->faker->unique()->safeEmail;
        $data = [
            'email' => $email,
            'password' => 'password123',
        ];

        // Enviar la solicitud de registro del usuario
        $response = $this->postJson('/api/register', $data);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que el usuario haya sido guardado en la base de datos
        $this->assertDatabaseHas('users', ['email' => $email]);

        // Verificar que se haya enviado una notificación de verificación de email
        Notification::assertSentTo(User::where('email', $email)->first(), EmailVerificationNotification::class);
    }

    /**
     * Prueba para verificar que el registro falla cuando se intenta usar un correo electrónico existente.
     */
    public function test_falla_registro_con_correo_existente()
    {
        // Crear un usuario en la base de datos con el correo proporcionado
        $user = User::factory()->create(['email' => 'sr4787814@gmail.com']);

        $data = [
            'email' => 'sr4787814@gmail.com',
            'password' => 'password123',
        ];

        // Realizar la solicitud de registro con un email existente
        $response = $this->postJson('/api/register', $data);

        // Verificar que la respuesta indique errores de validación
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Prueba para verificar que un usuario autenticado puede crear un perfil de cliente.
     */
    public function test_usuario_autenticado_puede_crear_perfil_de_cliente()
    {
        // Crear un usuario y generar un token JWT para autenticarlo
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $data = [
            'nombre' => 'John',
            'apellido' => 'Doe',
            'dui' => '01234567-8',
            'telefono' => '1234-5678',
            'id_tipo_persona' => 1,
            'es_contribuyente' => true,
            'id_estado' => 1,
            'id_departamento' => 1,
            'id_municipio' => 1,
            'direccion' => '123 Main St',
        ];

        // Enviar la solicitud de creación de perfil de cliente
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->postJson('/api/cliente/crear-perfil', $data);

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);

        // Verificar que el perfil de cliente se haya guardado en la base de datos
        $this->assertDatabaseHas('clientes', ['nombre' => 'John', 'id_user' => $user->id]);
    }

    /**
     * Prueba para verificar que un usuario no autenticado no puede crear un perfil de cliente.
     */
    public function test_usuario_no_autenticado_no_puede_crear_perfil_de_cliente()
    {
        $data = [
            'nombre' => 'John',
            'apellido' => 'Doe',
            'dui' => '01234567-8',
            'telefono' => '1234-5678',
            'id_tipo_persona' => 1,
            'es_contribuyente' => true,
            'id_estado' => 1,
            'id_departamento' => 1,
            'id_municipio' => 1,
            'direccion' => '123 Main St',
        ];

        // Realizar la solicitud sin autenticación
        $response = $this->postJson('/api/cliente/crear-perfil', $data);

        // Verificar que la respuesta sea 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * Prueba para verificar que un usuario puede iniciar sesión con credenciales válidas.
     */
    public function test_usuario_puede_iniciar_sesion_con_credenciales_validas()
    {
        // Crear un usuario con la contraseña hasheada
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $data = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        // Realizar la solicitud de inicio de sesión
        $response = $this->postJson('/api/login', $data);

        // Verificar que la respuesta sea exitosa y se incluya un token en la respuesta
        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'role', 'permissions', 'token']);
    }

    /**
     * Prueba para verificar que un usuario no puede iniciar sesión con credenciales inválidas.
     */
    public function test_usuario_no_puede_iniciar_sesion_con_credenciales_invalidas()
    {
        // Crear un usuario con la contraseña hasheada
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $data = [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ];

        // Realizar la solicitud de inicio de sesión con una contraseña incorrecta
        $response = $this->postJson('/api/login', $data);

        // Verificar que la respuesta sea 400 Bad Request y se incluya un mensaje de error
        $response->assertStatus(400);
        $response->assertJson(['message' => 'Login failed']);
    }

    /**
     * Prueba para verificar que un usuario inactivo no puede iniciar sesión.
     */
    public function test_impide_iniciar_sesion_a_usuarios_inactivos()
    {
        // Crear un usuario inactivo
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'status' => 0,  // Usuario inactivo
        ]);

        $data = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        // Realizar la solicitud de inicio de sesión
        $response = $this->postJson('/api/login', $data);

        // Verificar que la respuesta sea 403 Forbidden y se incluya un mensaje de cuenta inactiva
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Account is inactive']);
    }

    /**
     * Prueba para verificar que un usuario autenticado puede cerrar sesión correctamente.
     */
    public function test_usuario_puede_cerrar_sesion()
    {
        // Crear un usuario y generar un token JWT para autenticarlo
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Realizar la solicitud de cierre de sesión
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->postJson('/api/logout', ['token' => $token]);

        // Verificar que la respuesta sea exitosa y se indique que el usuario ha sido desconectado
        $response->assertStatus(200);
        $response->assertJson(['message' => 'User disconnected']);
    }

    /**
     * Prueba para verificar que un usuario autenticado puede recuperar su perfil.
     */
    public function test_usuario_autenticado_puede_recuperar_perfil()
    {
        // Crear un usuario y generar un token JWT para autenticarlo
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        // Realizar la solicitud para obtener el perfil del usuario
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->getJson('/api/user');

        // Verificar que la respuesta sea exitosa y se incluyan los datos del usuario
        $response->assertStatus(200);
        $response->assertJson(['user' => $user->toArray()]);
    }

    /**
     * Prueba para verificar que un usuario no autenticado no puede recuperar un perfil.
     */
    public function test_usuario_no_autenticado_no_puede_recuperar_perfil()
    {
        // Realizar la solicitud sin autenticación
        $response = $this->getJson('/api/user');

        // Verificar que la respuesta sea 401 Unauthorized
        $response->assertStatus(401);
    }

    /**
     * Prueba para verificar que un administrador puede asignar un rol a un usuario.
     */
    public function test_admin_puede_asignar_rol_a_usuario()
    {
        // Crear un administrador y un usuario, y generar un token JWT para el admin
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $role = Role::factory()->create();

        $token = JWTAuth::fromUser($admin);

        // Realizar la solicitud para asignar un rol al usuario
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/users/{$user->id}/assign-role", ['role_id' => $role->id]);

        // Verificar que la respuesta sea exitosa y que el usuario tenga el rol asignado
        $response->assertStatus(200);
        $this->assertTrue($user->fresh()->hasRole($role->name));
    }

    /**
     * Prueba para verificar que no se puede asignar un rol inexistente a un usuario.
     */
    public function test_impide_asignar_rol_inexistente_a_usuario()
    {
        // Crear un administrador y un usuario, y generar un token JWT para el admin
        $admin = User::factory()->create();
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($admin);

        // Realizar la solicitud para asignar un rol inexistente
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson("/api/users/{$user->id}/assign-role", ['role_id' => 999]);

        // Verificar que la respuesta sea 422 Unprocessable Entity y se incluya un mensaje de error de validación
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role_id']);
    }

    // Limpieza de datos si es necesario
    protected function tearDown(): void
    {
        // Si necesitas eliminar datos específicos que has creado durante las pruebas
        User::where('email', 'sr4787814@gmail.com')->delete();
        Clientes::where('nombre', 'John')->delete();

        parent::tearDown();
    }
}
