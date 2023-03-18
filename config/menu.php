<?php

return [
    [
        'items' => [
            [
                'route' => 'admin.home',
                'active' => 'admin.home*',
                'label' => 'Home',
                'icon' => 'o_home',
            ]
        ]
    ],
    // [
    //     'label' => 'EVENTOS',
    //     'items' => [
    //         [
    //             'route' => 'admin.meeting.index',
    //             'active' => 'admin.meeting*',
    //             'label' => 'Encontros',
    //             'icon' => 'chat_bubble_outline',
    //             'can' => 'meeting_index'
    //         ],
    //         [
    //             'route' => 'admin.live-event.index',
    //             'active' => 'admin.live-event*',
    //             'label' => 'Eventos ao vivo',
    //             'icon' => 'wifi',
    //             'can' => 'live_event_index'
    //         ],
    //         [
    //             'route' => 'admin.campaign.index',
    //             'active' => 'admin.campaign*',
    //             'label' => 'Campanhas',
    //             'icon' => 'sym_o_import_contacts',
    //             'can' => 'campaign_index'
    //         ],
    //         [
    //             'route' => 'admin.schedule.index',
    //             'active' => 'admin.schedule*',
    //             'label' => 'Agenda',
    //             'icon' => 'o_calendar_today',
    //             'can' => 'schedule_index'
    //         ],
    //         [
    //             'route' => 'admin.quizz.index',
    //             'active' => 'admin.quizz*',
    //             'label' => 'Quizz',
    //             'icon' => 'o_edit',
    //             'can' => 'quizz_index'
    //         ],
    //         [
    //             'route' => 'admin.notification.index',
    //             'active' => 'admin.notification*',
    //             'label' => 'Notificações',
    //             'icon' => 'o_notifications',
    //             'can' => 'notification_index'
    //         ],
    //     ]
    // ],
    [
        'label' => 'CADASTROS',
        'items' => [
            [
                'route' => 'admin.course.index',
                'active' => 'admin.course*|admin.module*',
                'label' => 'Cursos',
                'icon' => 'o_school',
                'can' => 'course_index'
            ],
            [
                'route' => 'admin.certificate.index',
                'active' => 'admin.certificate*',
                'label' => 'Certificados',
                'icon' => 'o_card_membership',
                'can' => 'certificate_index'
            ],
            [
                'route' => 'admin.category.index',
                'active' => 'admin.category*',
                'label' => 'Categorias',
                'icon' => 'list_alt',
                'can' => 'category_index'
            ],
            [
                'route' => 'admin.product.index',
                'active' => 'admin.product*',
                'label' => 'Produto',
                'icon' => 'local_mall',
                'can' => 'product_index'
            ],
            [
                'route' => 'admin.student.index',
                'active' => 'admin.student*',
                'label' => 'Alunos',
                'icon' => 'o_school',
                'can' => 'student_index'
            ],
            // [
            //     'route' => 'admin.job-vacancy.index',
            //     'active' => 'admin.job-vacancy*',
            //     'label' => 'Vagas',
            //     'icon' => 'work_outline',
            //     'can' => 'job_vacancy_index'
            // ],
            // [
            //     'route' => 'admin.partner.index',
            //     'active' => 'admin.partner*',
            //     'label' => 'Parceiros',
            //     'icon' => 'alternate_email',
            //     'can' => 'partner_index'
            // ],
            // [
            //     'route' => 'admin.common-question.index',
            //     'active' => 'admin.common-question*',
            //     'label' => 'F.A.Q.',
            //     'icon' => 'help_outline',
            //     'can' => 'common_question_index'
            // ],
        ]
    ],
    [
        'label' => 'SISTEMA',
        'items' => [
            [
                'route' => 'admin.user.index',
                'active' => 'admin.user*',
                'label' => 'Usuários',
                'icon' => 'o_person',
                'can' => 'user_index'
            ],
            [
                'route' => 'admin.role.index',
                'active' => 'admin.role*',
                'label' => 'Grupos de permissão',
                'icon' => 'o_group',
                'can' => 'role_index'
            ],
        ]
    ]
];
