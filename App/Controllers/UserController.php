<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Models\User;
use App\Helpers\View;

class UserController {

    private $responseMessage = array();

    public function index($request, $response, $args) {
        AuthHelper::restrictToPermission(User::USER_LEVEL_ADMIN);
        $users = User::findByRole([User::USER_LEVEL_UFFS, User::USER_LEVEL_ADMIN, User::USER_CO_ORGANIZER, User::USER_LEVEL_EXTERNAL]);

        $authUser = AuthHelper::getAuthenticatedUser();

        View::render('layout/admin/header', array('user' => $authUser));
        View::render('auth/users', array('users' => $users, 'authUser' => $authUser));
        View::render('layout/admin/footer');

        return $response;
    }

    public function update($request, $response, $args) {
        AuthHelper::restrictToPermission(User::USER_LEVEL_ADMIN, "JSON");

        if(isset($_REQUEST['type']) && isset($args['id'])){
            $userNewRole = $_REQUEST['type'];
            $id = $args['id'];
            $user = User::findById($id);
            $user->type = $userNewRole;
            $user->save();

            return $response->withAddedHeader('message', "Permissao atualizada com sucesso!");
        }

        $response = $response->withAddedHeader('message', "Ocorreu algum erro.");
        return $response->withStatus(500);
    }

}

