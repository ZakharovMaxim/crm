<?php

    namespace App\Http\Controllers;

    use App\User;
    use App\Shop;
    use Illuminate\Http\Request;
    use App\Http\Requests\StoreUserRequest;
    use App\Http\Requests\UpdateUserRequest;
    use App\Http\Requests\UpdateUserPasswordRequest;
    use App\Http\Requests\StoreImageRequest;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Storage;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;

    class UserController extends Controller
    {
        public function show(User $user)
        {
            $user->role = unserialize($user->role);
            return $user;
        }
        public function set_image(User $user, StoreImageRequest $request)
        {
            $old_image = $user->image;
            if ($old_image) {
                Storage::delete("public/" . basename($old_image));
            }
            $photo = $request->file('image');
            $path = '/storage/'.basename($photo->store('public'));
            $user->image = $path;
            $user->save();
            return $path;
        }
        public function delete_image(User $user, Request $request)
        {
            $old_image = $user->image;
            if ($old_image) {
                Storage::delete("public/" . basename($old_image));
                $user->image = null;
                $user->save();
            }
            return 'ok';
        }
        public function roles_info()
        {
            $shops = Shop::where(['is_deleted' => 0])->get();
            $modules = AppController::get_all_modules();
            return compact('shops', 'modules');
        }
        public function set_role(User $user, Request $request)
        {
            $rules = $request->input('rules');
            if (!$rules) return 'ok';
            $user->role = serialize($rules);
            $user->save();
            return 'ok';
        }
        public function change_password(User $user, UpdateUserPasswordRequest $request)
        {
            $user->update([
                'password' => Hash::make($request->input('password'))
            ]);
            return 'ok';
        }
        public function update(User $user, UpdateUserRequest $request)
        {
            $user->update($request->all());
            
            return 'ok';
        }
        public function authenticate(Request $request)
        {
            $credentials = $request->only('login', 'password');

            return self::create_token($credentials);
        }
        static private function refresh_token()
        {
            $token = JWTAuth::getToken();
            try {
                $token = JWTAuth::refresh($token);
            } catch(TokenInvalidException $e){
                throw new AccessDeniedHttpException('The token is invalid');
            }
            return $token;
        }
        static private function create_token ($credentials)
        {
            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
                $user = JWTAuth::user();
                if ($user->is_deleted) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

            return $token;
        }
        public function index ()
        {
            $user = JWTAuth::user();
            return User::where('id', '!=', $user->id)->get();
        }
        public function store(StoreUserRequest $request)
        {
            $user = User::create([
                'name' => $request->get('name'),
                'surname' => $request->get('surname'),
                'login' => $request->get('login'),
                'role' => serialize($request->get('role')),
                'password' => Hash::make($request->get('password')),
            ]);

            return $user;
        }
        public function destroy(User $user)
        {
            $user->is_deleted = true;
            $user->login = $user->login.'[DELETED]';
            $user->save();
            return 'ok';
        }
        public function restore(User $user)
        {
            $user->is_deleted = false;
            $user->login = str_replace('[DELETED]', '', $user->login);
            $user->save();
            return 'ok';
        }
        public function getAuthenticatedUser()
            {
                    try {

                        if (! $user = JWTAuth::parseToken()->authenticate()) {
                                return response()->json(['user_not_found'], 404);
                        }

                    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                            return response()->json(['token_expired'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                            return response()->json(['token_invalid'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                            return response()->json(['token_absent'], $e->getStatusCode());

                    }

                    return response()->json(compact('user'));
            }
    }