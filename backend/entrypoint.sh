until mysql -h mysql -uuser -puser -e "SELECT 1" &> /dev/null
do
  echo "Esperando o banco de dados iniciar..."
  sleep 3
done

echo "Banco de dados disponível! Rodando migrations..."

php artisan migrate --force

# Depois sobe o servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000
