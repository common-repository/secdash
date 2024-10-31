<?php

namespace Baseplus\Secdash;

class ApiEndpoint
{
    //const VERSION  = '1.4.1';
    const API_HOST = 'api.secdash.de';
    const API_URL  = 'https://api.secdash.de/api/';

    static public function exec()
    {
        if ('POST' !== $_SERVER['REQUEST_METHOD'] || 'https' !== $_SERVER['REQUEST_SCHEME']) {
            return;
        }

        if (0 !== strpos($_SERVER['REQUEST_URI'], '/secdash-monitoring-endpoint/')) {
            return;
        }

        if (false === isset($_POST['access_token'])) {
            return;
        }

        $token = \Secdash::getOption('access_token');
        if (empty($token)) {
            return; // does it make sense to return an error so the backend knows that the plugin is not yet configured?
        }

        if ($token !== $_POST['access_token']) {
            return;
        }

        if ($_SERVER['REMOTE_ADDR'] !== gethostbyname(self::API_HOST)) {
            return;
        }

        if (false === function_exists('json_encode')) {
            return;
        }

        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'get_plugins':
                    $result = self::getPlugins();
                    break;
                case 'get_metrics':
                    $result = self::getMetrics();
                    break;
                case 'get_themes':
                    $result = self::getThemes();
                    break;
                case 'get_users':
                    $result = self::getUsers();
                    break;
                case 'get_all':
                    $result = self::getMetrics();
                    $result = array_merge($result, self::getPlugins());
                    $result = array_merge($result, ['users' => self::getUsers()]);
                    $result = array_merge($result, ['themes' => self::getThemes()]);
                    break;
                default:
                    $result = [];
                    break;
            }

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($result);
            die();
        }
    }

    /**
     * @return array
     */
    static private function getMetrics()
    {
        global $wp_version;

        return [
            'php_version'       => phpversion(),
            'secdash_version'   => SECDASH_PLUGIN_VERSION,
            'wordpress_version' => $wp_version,
            'is_multisite'      => is_multisite(),
        ];
    }

    /**
     * @return array[]
     */
    static private function getPlugins()
    {
        $result = ['plugins' => [], 'mu-plugins' => []];

        foreach (get_plugins() as $key => $info) {
            $info['IsActive'] = is_plugin_active($key);
            $result['plugins'][$key] = $info;
        }

        $result['mu-plugins'] = get_mu_plugins();

        return $result;
    }

    /**
     * @return array
     */
    static private function getUsers()
    {
        $result = [];

        foreach (get_users() as $user) {
            /** @var \WP_User $user */
            $result[] = [
                'id' => $user->ID,
                'login' => $user->user_login,
                'nicename' =>$user->user_nicename,
                'displayname' =>$user->display_name,
                'email' => $user->user_email,
                'registered' => $user->user_registered,
                'status' => $user->user_status,
                'deleted' => $user->deleted,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    static private function getThemes()
    {
        $result = [];

        $current = wp_get_theme();
        foreach (wp_get_themes() as $theme) {

            $isActive = $current->get('Name') === $theme->get('Name') &&
                $current->get_stylesheet() === $theme->get_stylesheet() &&
                $current->get_template() === $theme->get_template();

            $result[] = [
                'name' => $theme->get('Name'),
                'theme_uri' => $theme->get('ThemeURI'),
                'description' => $theme->get('Description'),
                'author' => $theme->get('Author'),
                'author_uri' => $theme->get('AuthorURI'),
                'version' => $theme->get('Version'),
                'requires_wp' => $theme->get('RequiresWP'),
                'requires_php' => $theme->get('RequiresPHP'),
                'stylesheet' => $theme->get_stylesheet(),
                'template' => $theme->get_template(),
                'update' => $theme->update,
                'active' => $isActive,
            ];
        }

        return $result;
    }

    static public function execBackendRegistration()
    {
        $data = [
            'url' => get_home_url(),
        ];

        var_dump(self::execApiRequest($data));
    }

    static private function execApiRequest($data)
    {
        if (false === function_exists('curl_init')) {
            return 'ERROR: ext-curl missing';
            // todo check for alternative ways e.g. file_get_contents()
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, self::API_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if (false === $response) {
            return 'ERROR: ' . curl_errno($curl) . ' ' . curl_error($curl);
        }

        if (200 !== $status) {
            return 'ERROR: ' . $status;
        }

        $data = json_decode($response, true);
        if (false === $data) {
            return 'ERROR: malformed response';
        }

        return $data;
    }

    /*
    static public function onActivation() { }
    static public function onDeactivation() { }
    */

    static public function onUninstall()
    {
        delete_option('secdash');
    }
}
