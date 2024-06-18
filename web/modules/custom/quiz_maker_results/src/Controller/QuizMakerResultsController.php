<?php

namespace Drupal\quiz_maker_results\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class QuizMakerResultsController extends ControllerBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Construtor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   O serviço de gerenciador de tipo de entidade.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   O serviço de renderização.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer) {
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer')
    );
  }

  /**
   * Função para exibir todos os resultados dos quizzes.
   */
  public function displayAllQuizResults() {
    $output = [];

    // Verifique se a entidade 'quiz_result' existe
    if ($this->entityTypeManager->hasDefinition('quiz_result')) {
      // Obter o armazenamento para 'quiz_result'
      $storage = $this->entityTypeManager->getStorage('quiz_result');

      // Consultar todos os resultados do quiz
      $query = $storage->getQuery()
        ->accessCheck(TRUE)
        ->execute();
      // Carregar todos os resultados
      $quiz_results = $storage->loadMultiple($query);
      $quiz_summary = [];
      foreach ($quiz_results as $result) {
        if($result->getQuiz()) {
          $quiz_id = $result->getQuiz()->get('id')->value;

          // Inicializar contadores se o quiz_id não estiver ainda no array
          if (!isset($quiz_summary[$quiz_id])) {
            $quiz_summary[$quiz_id] = [
              'total_responses' => 0,
              'responses' => [],
              'label' => $result->label(),
            ];
          }

          // Incrementar o total de respostas
          $quiz_summary[$quiz_id]['total_responses']++;

          // Obter respostas
          $responses = $result->get('responses')->referencedEntities();
          foreach ($responses as $response) {
            if($response != NULL) {
              $question = $response->getQuestion();
              if($question != NULL) {
                $questionContent = $question->getQuestion();
                $questionLabel = $question->get('label')->value;
                // Inicializar contadores de votos por valor de resposta se necessário
                if (!isset($quiz_summary[$quiz_id]['responses'][$questionLabel])) {
                  $quiz_summary[$quiz_id]['responses'][$questionLabel] = [
                    'label' => $questionLabel,
                    'content' => $questionContent,
                    'value' => []
                  ];
                }
                foreach ($question->getAnswers() as $key => $value) {
                  if(in_array($value->get('id')->value, $response->getResponses())) {
                    $response_value = strip_tags($value->getAnswer());

                    // Inicializar contadores de votos por valor de resposta se necessário
                    if (!isset($quiz_summary[$quiz_id]['responses'][$questionLabel]['value'][$response_value])) {
                      $quiz_summary[$quiz_id]['responses'][$questionLabel]['value'][$response_value] = 0;
                    }

                    // Incrementar o total de votos para o valor de resposta
                    $quiz_summary[$quiz_id]['responses'][$questionLabel]['value'][$response_value]++;
                  }
                }
              }
            }
          }
        }
      }

      // Preparar a saída para renderização
      foreach ($quiz_summary as $quiz_id => $summary) {
        $output[] = [
          'quiz_id' => $quiz_id,
          'label' => $summary['label'] ?? " - ",
          'total_responses' => $summary['total_responses'] ?? " - ",
          'responses' => $summary['responses'],
        ];
      }
    }

    if(empty($output))
      \Drupal::messenger()->addError(t('The quizzes dont have any results yet.'));

    return [
      '#theme' => 'quiz-maker-results',
      '#quiz_summary' => $output,
    ];
  }
  /**
   * Custom view for quiz success.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response containing the quiz result.
   */
  public function successQuiz($quiz_result) {
    return [
      '#theme' => 'success-quiz',
      '#message' => '<p class="pl-5"><strong>Quiz Completo!</strong> Parabéns, você concluiu o quiz com sucesso! Seus resultados foram enviados.</p>
        <p class="ml-5">Agradecemos pelo seu tempo e esforço em participar deste quiz. Suas respostas são muito valiosas para nós. <br>
        Se tiver alguma dúvida ou precisar de mais informações, não hesite em entrar em contato com nossa equipe.</p>',
    ];
  }

  /**
   * Custom view for quiz.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response containing the quiz result.
   */
  public function showQuiz($quiz) {
    $output = [];

    // Verifique se a entidade 'quiz' existe
    if ($this->entityTypeManager->hasDefinition('quiz')) {
      // Obter o armazenamento para 'quiz'
      $storage = $this->entityTypeManager->getStorage('quiz');

      // Consultar todos os resultados do quiz
      $query = $storage->getQuery()
        ->condition('id', $quiz)
        ->accessCheck(TRUE)
        ->execute();
      // Carregar todos os resultados
      $quiz_data = $storage->loadMultiple($query);
      $quiz_data = reset($quiz_data);

      $output = [
        "label" => $quiz_data->get("label")->value,
        "description" => $quiz_data->get("description")->value
      ];
    }

    if(empty($output))
      \Drupal::messenger()->addError(t('The quizzes dont have any results yet.'));
    return [
      '#theme' => 'show-quiz',
      '#id' => $quiz,
      '#content' => $output
    ];
  }

  /**
   * Custom api for quiz.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response containing the quiz result.
   */
  public function apiListQuizzes() {
    $output = [];

    // Verifique se a entidade 'quiz_result' existe
    if ($this->entityTypeManager->hasDefinition('quiz_result')) {
      // Obter o armazenamento para 'quiz_result'
      $storage = $this->entityTypeManager->getStorage('quiz_result');

      // Consultar todos os resultados do quiz
      $query = $storage->getQuery()
        ->accessCheck(TRUE)
        ->execute();
      // Carregar todos os resultados
      $quiz_results = $storage->loadMultiple($query);
      $quiz_summary = [];
      foreach ($quiz_results as $result) {
        if($result->getQuiz()) {
          $quiz_id = $result->getQuiz()->get('id')->value;

          // Inicializar contadores se o quiz_id não estiver ainda no array
          if (!isset($quiz_summary[$quiz_id])) {
            $quiz_summary[$quiz_id] = [
              'total_responses' => 0,
              'responses' => [],
              'label' => $result->label(),
            ];
          }

          // Incrementar o total de respostas
          $quiz_summary[$quiz_id]['total_responses']++;

          // Obter respostas
          $responses = $result->get('responses')->referencedEntities();
          foreach ($responses as $response) {
            if($response != NULL) {
              $question = $response->getQuestion();
              if($question != NULL) {
                $questionContent = $question->getQuestion();
                $questionLabel = $question->get('label')->value;
                // Inicializar contadores de votos por valor de resposta se necessário
                if (!isset($quiz_summary[$quiz_id]['responses'][$questionLabel])) {
                  $quiz_summary[$quiz_id]['responses'][$questionLabel] = [
                    'label' => $questionLabel,
                    'content' => $questionContent,
                    'value' => []
                  ];
                }
                foreach ($question->getAnswers() as $key => $value) {
                  if(in_array($value->get('id')->value, $response->getResponses())) {
                    $response_value = strip_tags($value->getAnswer());

                    // Inicializar contadores de votos por valor de resposta se necessário
                    if (!isset($quiz_summary[$quiz_id]['responses'][$questionLabel]['value'][$response_value])) {
                      $quiz_summary[$quiz_id]['responses'][$questionLabel]['value'][$response_value] = 0;
                    }

                    // Incrementar o total de votos para o valor de resposta
                    $quiz_summary[$quiz_id]['responses'][$questionLabel]['value'][$response_value]++;
                  }
                }
              }
            }
          }
        }
      }

      // Preparar a saída para renderização
      foreach ($quiz_summary as $quiz_id => $summary) {
        $output[] = [
          'quiz_id' => $quiz_id,
          'label' => $summary['label'] ?? " - ",
          'total_responses' => $summary['total_responses'] ?? " - ",
          'responses' => $summary['responses'],
        ];
      }
    }

    if(empty($output))
      \Drupal::messenger()->addError(t('The quizzes dont have any results yet.'));


    // Retorna os dados como JSON.
    return new JsonResponse($output);
  }

}
