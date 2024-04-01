<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess()
    {
        $this->post('/api/v1/users', [
            'name' => 'John Doe',
            'email' => 'John.Doe@gmail.com',
            'password' => '123456789',
            'password_confirmation' => '123456789'
        ])->assertStatus(201)
            ->assertJson([
                "data"=>[
                    "name"=>"John Doe",
                    "email"=>"John.Doe@gmail.com"
                ]
        ]);
    }
    public function testRegisterfailed()
    {
        $this->post('/api/v1/users', [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => ''
        ])->assertStatus(400)
            ->assertJson([
                "errors"=>[
                    "name"=>[
                        "The name field is required."
                    ],
                    "email"=>[
                        "The email field is required."
                    ],
                    "password"=>[
                        "The password field is required."
                    ]
                ]
            ]);
    }
    public function testRegisterfailedvalidation()
    {
        $this->testRegisterSuccess();
        $this->post('/api/v1/users', [
            'name' => 'mitchell admin',
            'email' => 'John.Doe2gmail.com',
            'password' => '123456789',
            'password_confirmation' => '123456789'
        ])->assertStatus(400)
            ->assertJson([
                "errors"=>[
                    "email"=>[
                        "The email has already been taken."
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->testRegisterSuccess();
        $this->post('/api/v1/users/login',[
            'email' => 'John.Doe@gmail.com',
            'password' => '123456789'
        ])->assertStatus(200)
            ->assertJson([
                "data"=>[
                    "name"=>"John Doe",
                    "email"=>"John.Doe@gmail.com"
                ]
            ]);
        
        $user - User::where('email','John.Doe@gmail.com')->first();
        self::assertNotNull($user->remember_token);
    }

    public function testLoginFailedEmail()
    {
        $this->post('/api/v1/users/login',[
            'email'=> 'John.Doe@gmail.com',
            'password' => '123456789'
        ])->assertStatus(401)
            ->assertJson([
                'errors'=>[
                    'message'=>['username or password wrong']
                ]     
            ]);
    }

    public function testLoginFailedPassword()
    {
        $this->testRegisterSuccess();
        $this->post('/api/v1/users/login', [
            'email'=> 'John.Doe@gmail.com',
            'password' => 'password123456789'
        ])->assertStatus(401)
        ->assertJson([
            'errors'=>[
                'message'=>['username or password wrong']
            ]
        ]);    
    }
}
