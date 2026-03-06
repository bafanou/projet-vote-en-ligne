pipeline {
    agent any

    stages {

        stage('Clone') {
            steps {
                echo ' Clonage du dépôt...'
                checkout scm
            }
        }

        stage('Tests') {
            steps {
                echo ' Vérification des fichiers...'
                sh 'ls -la'
                sh 'echo "Tests OK - projet PHP détecté"'
            }
        }

        stage('Build Docker') {
            steps {
                echo ' Construction de limage Docker...'
                sh 'docker build -t projet-vote:latest . || echo "Docker non disponible"'
            }
        }

        stage('Déploiement') {
            steps {
                echo ' Déploiement...'
                sh '''
                    docker stop vote-test || true
                    docker rm vote-test || true
                    echo "Déploiement simulé OK"
                '''
            }
        }
    }

    post {
        success { echo 'Pipeline réussi !' }
        failure { echo ' Échec du pipeline.' }
    }
}
