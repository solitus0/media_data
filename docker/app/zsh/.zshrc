[[ ! -f ~/.p10k.zsh ]] || source ~/.p10k.zsh
[[ ! -f ~/.phpdebug ]] || source ~/.phpdebug


ZSH_THEME="agnoster"

# PLUGINS

plugins=(
  git
  osx
  zsh-autosuggestions
  last-working-dir
  sudo
  history
)

alias zshrc='vim ~/.zshrc'
alias rr='source ~/.zshrc'
alias work='cd /var/www'

[[ ! -f ~/.oh-my-zsh/custom/themes/powerlevel10k/powerlevel10k.zsh-theme ]] || source ~/.oh-my-zsh/custom/themes/powerlevel10k/powerlevel10k.zsh-theme
