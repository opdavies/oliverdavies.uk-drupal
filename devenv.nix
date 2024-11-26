{ pkgs, ... }:

let
  drupal = {
    docroot = "web";
    theme = {
      name = "opdavies";
      path = "${drupal.docroot}/themes/custom/${drupal.theme.name}";
    };
  };
in
{
  packages = with pkgs; [ git ];

  dotenv.disableHint = true;

  languages = {
    javascript = {
      enable = true;
      npm.enable = true;
    };

    php = {
      enable = true;
      version = "8.2";

      ini = ''
        memory_limit = 256M
      '';

      fpm.pools.web = {
        listen = "127.0.0.1:9000";

        settings = {
          "pm" = "dynamic";
          "pm.max_children" = 75;
          "pm.max_requests" = 500;
          "pm.max_spare_servers" = 20;
          "pm.min_spare_servers" = 5;
          "pm.start_servers" = 10;
        };
      };
    };
  };

  services = {
    caddy = {
      enable = true;

      config = ''
        {
          http_port 8080
        }

        localhost:8080 {
          root * ${drupal.docroot}
          encode gzip
          php_fastcgi localhost:9000
          file_server
        }
      '';
    };

    mysql = {
      enable = true;
      initialDatabases = [ { name = "app"; } ];
    };
  };

  processes = {
    tailwind.exec = ''
      cd ${drupal.theme.path}
      watchexec --exts css,twig tailwindcss --config assets/tailwind.config.ts \
        --output dist/tailwind.css
    '';
  };

  enterShell = ''
    if [[ ! -d vendor ]]; then
      composer install
    fi

    if [[ ! -d ${drupal.theme.path}/node_modules ]]; then
      cd "${drupal.theme.path}" \
        && npm clean-install
    fi
  '';

  enterTest = ''
    phpunit --testdox
  '';
}
