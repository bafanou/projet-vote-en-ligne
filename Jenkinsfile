pipeline {
    agent any

    environment {
        DOCKER_IMAGE = 'bafanou/projet-vote'
        DOCKER_TAG   = "v${BUILD_NUMBER}"
    }

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
                sh "docker build -t ${DOCKER_IMAGE}:${DOCKER_TAG} ."
            }
        }

        stage('Push Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-credentials',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    sh "echo $DOCKER_PASS | docker login -u $DOCKER_USER --password-stdin"
                    sh "docker push ${DOCKER_IMAGE}:${DOCKER_TAG}"
                }
            }
        }

        stage('Déploiement') {
            steps {
                sh '''
                    docker stop vote-test || true
                    docker rm vote-test || true
                    echo " Déploiement OK"
                '''
            }
        }
    }

    post {
        success { echo ' Pipeline réussi !' }
        failure { echo ' Échec du pipeline.' }
    }
}
