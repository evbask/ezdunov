echo "Ok"
commit_message=$1

echo $commit_message
if [[ $commit_message == '' ]]
then
	espeak  -s130 "no commit  message"
	exit
fi
git add -A
git commit -m "$commit_message"
git push
espeak  -s130 "git push done"
ssh root@46.36.216.211 'cd /var/www/ezdunov.ru; git pull'
espeak  -s130 "server pull done"
