<?php

return [
    "operation" => [

        "VDP Personal Info" => [
            "route" => "#",
            "icon" => "fa-user",
            "children" => [
                'Entry List' => [
                    'route' => 'operation.info.index',
                    'icon' => 'fa-cog',
                ],
                'Import List' => [
                    'route' => 'operation.info.import',
                    'icon' => 'fa-cog',
                ],
                'Import Images' => [
                    'route' => 'operation.image_import',
                    'icon' => 'fa-cog',
                ],
                "Entry" => [
                    "route" => "operation.info.create",
                    "icon" => "fa-dashboard",
                ],

                "Verify Entry(BULK)" => [
                    "route" => "operation.chunk_verify",
                    "icon" => "fa-dashboard",
                ],
                /*           "View Entry info" => [
                               "route" => "entry_info",
                               "icon" => "fa-dashboard",                          ],
             /*
                           "Print ID Card" => [
                               "route" => "print_card_id_view",
                               "icon" => "fa-dashboard",
                           ],  */
                "VDP Picture and Signature" => [
                    "route" => "pic_signature_info",
                    "icon" => "fa-dashboard",
                ],

            ]
        ],
        "Service" => [
            "route" => "#",
            "icon" => "fa-suitcase",
            "children" => [
                "Print ID Card" => [
                    "route" => "operation.print_card_id_view",
                    "icon" => "fa-dashboard",
                ],
            ]
        ],
        "Report" => [
            "route" => "#",
            "icon" => "fa-list-alt",
            "children" => [
                "ID Print List" => [
                    "route" => "operation.print_id_list",
                    "icon" => "fa-dashboard",
                ],
            ]
        ],
        "General Settings" => [
            "route" => "#",
            "icon" => "fa-cogs",
            "children" => [
                "Range Setting" => [
                    "route" => "operation.range.index",
                    "icon" => "fa-cog",
                ],
                "Unit Setting" => [
                    "route" => "operation.unit.index",
                    "icon" => "fa-cog",
                ],
                "Thana Setting" => [
                    "route" => "operation.thana_view",
                    "icon" => "fa-cog",
                ],
                "Union Setting" => [
                    "route" => "operation.union.index",
                    "icon" => "fa-cog",
                ]
            ]
        ]
    ]
];