# Ideia do projeto

##IDEIA DA NECESSIDADE:

##Usuário logar no sistema, e o sistema vai checar as permissões dele.
###Tipos de checagem:
    Permissões por usuário (Permissions);
    Papeis (Roles);
    Filiais (Branches);

###Usuário pode ter permissão de criar contas na filial 1, porém na filial 2 só pode visualizar.

##IDEIA DA ACL:
    A ideia é ser algo mais amplo para poder ser reaproveitado em vários lugares.

    Então o usuário poderá ter permissões cadastradas para ele (permissions && users -> permission_user):
    Poderá fazer parte de papeis (roles && users -> role_user)
    Poderá fazer parte de filiais (branches && roles && users -> groups)

###IDEIA PARA CHECAR PERMISSÕES:
    $user->hasPermission('permissão'); -> Checa se o usuário tem a permissão X,
    tanto diretamente por ele, ou em alguma role que ele faz parte.

    $user->hasPermission(['permissão1', 'permissão2'], true);->Checa se o usuário tem as permissões
    Se o segundo parâmetro for verdadeiro, verifica se tem uma ou outra permissão, se for falta exigir que
    tenha acesso a todas as permissões

    $user->hasPermission(['permissão1', 'permissão2'], false, 1); -> o Terceiro parametro é a branch (filial),
    caso ele seja informado, verificar se o usuário tem permissão naquela filial específica,
    (desprezando, se ele tem diretamente ligado a ele a permissão, isso pode ser mudado no futuro).

#Middleware
    A pensar

#Helpers
    A pensar


==== novas ideias
Pensando no uso do defender, acrescentar estas duas tabelas:
tenant: {id, name}
role_tenant: (tenant_id, role_id, user_id);

eu preciso checar a filial atual que o cara está, blz isso.
filial_atual e user_id está em (role_tenant)? sim:
    role_id tem a permissao X? sim:
    allow:
    Não:
    deny

if (canDo('post-update')) {
    return $post->filial_id==$user->hasPermissionByFilial('post-update', $post->filial_id);
}

======================
permissions : {id, name, slug}
roles       : {id, name, slug}
tenants     : {id, name}

permission_role : {permission_id, role_id}
role_user       : {role_id, user_id}

tenant_user     : {tenant_id, user_id, role_id}

"tenant->hasUser(user_id)"
user->hasTenant(tenant_id)
    ->hasPermission...


##Publicar config e migrates
php artisan vendor:publish --provider="ResultSystems\Acl\AclServiceProvider"



#Como usar nas rotas
```
Route::get('/path', ['middleware' => ['auth', 'needsPermission'],
    'permission'               => ['permission.11', 'permission.12'],
    'any'                      => false, //any=true (qualquer permissão) (opcional)
    'branch_id'                => 1, // Empreas/filial (opcional)
    function () {
        dd('Tenho permissão');
    }]);
```

```
Route::get('/path', [
    'middleware' => ['auth', 'needsPermission:permission.5|permission.10,true,5'],

    function () {
        dd('Tenho permissão');
    }]);

    //needsPermission=middleware
    //permission.5=permissão 5
    //permission.10=permissão 10
    //any=true (qualquer permissão) (opcional)
    //1=Filial/Empresa (opcional)
```
