{
  inputs.nixpkgs.url = "github:nixos/nixpkgs/nixpkgs-unstable";

  outputs = { nixpkgs, ... }:
    let
      system = "x86_64-linux";
      pkgs = nixpkgs.legacyPackages.${system};

      inherit (pkgs) just mkShell nodejs;
    in {
      devShells.${system}.default = mkShell { buildInputs = [ just nodejs ]; };
    };
}
