<?php
    defined( 'ABSPATH' ) OR exit;

    /**
     * Gamipress - Ranking
     *
     * @package     pc-gamipress-ranking
     * @author      Rafael Carneiro de Moraes
     * @copyright   2019 Rafael Carneiro de Moraes
     * @license     GPL-3.0+
     *
     * @wordpress-plugin
     * Plugin Name: Gamipress - Ranking
     * Plugin URI:  https://github.com/rafinhacarneiro/gamipress-ranking
     * Description: Ranking para o Gamipress. Adicione [ranking] em um post para gerar. Atributos disponíveis: type (tipo dos pontos) e limit (quantidade de registros)
     * Version:     1.0.0
     * Author:      Rafael Carneiro de Moraes
     * Author URI:  https://rafinhacarneiro.github.io
     * Text Domain: pc-gamipress-ranking
     * License:     GPL-2.0+
     * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
     */

    // Add Shortcode
    function pc_gamipress_ranking_show_html($atts = []){

        // Receive and sanitize input data
        $a = shortcode_atts(array(
            "type" => "connects",
            "limit" => null,
            "order" => "DESC"
        ), $atts);

        $type = trim(strtolower($a["type"]));
        $limit = intval($a["limit"]);
        $order = trim(strtolower($a["order"]));

        // Query for the leaderboard
        global $wpdb;

        $sql = "SELECT user.display_name AS name, SUM(game.points) AS points, game.points_type AS type FROM wppc_gamipress_user_earnings AS game INNER JOIN wppc_users AS user ON game.user_id = user.ID WHERE game.points_type = '{$type}' GROUP BY name, type ORDER BY points {$order}";

        if(!empty($limit)){
            $sql .= " LIMIT {$limit}";
        }

        if($result = $wpdb->get_results($sql, ARRAY_A)){

            // Setup the ranking table
            $leaderboard = "
                <div>
                    <div class='ranking-coin-place'>
                        <span class='ranking-coin'>{$type}</span>
                    </div>
                </div>
                <table class='ranking'>
                    <thead>
                        <th class='ranking-header'>Posição</th>
                        <th class='ranking-header'>Nome</th>
                        <th class='ranking-header'>Pontos</th>
                    </thead>
                    <tbody>";

            $i = 1;

            // Setup results
            foreach($result as $position){
                $leaderboard .= "<tr>";
                $leaderboard .= "   <td class='ranking-body'>{$i}º</td>";
                $leaderboard .= "   <td class='ranking-body'>".$position["name"]."</td>";
                $leaderboard .= "   <td class='ranking-body'>".$position["points"]."</td>";
                $leaderboard .= "</tr>";

                $i++;
            }

            // End table setup
            $leaderboard .= "
                    </tbody>
                </table>";

            // Show the leaderboard
            return $leaderboard;
        }

        return "<p>Ranking não disponível</p>";

    }

    // Add/remove custom CSS to Wordpress
    function pc_gamipress_ranking_load_css(){
        wp_register_style('pc-gamipress-ranking', plugins_url('pc-gamipress-ranking.css', __FILE__));
        wp_enqueue_style('pc-gamipress-ranking', plugins_url('pc-gamipress-ranking.css', __FILE__));
    }

    add_shortcode('ranking', 'pc_gamipress_ranking_show_html');
    add_action("wp_enqueue_scripts", "pc_gamipress_ranking_load_css");

?>
