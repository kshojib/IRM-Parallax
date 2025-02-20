<?php
if (!defined('ABSPATH')) {
    exit;
}

class IRM_Parallax_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'irm_parallax';
    }

    public function get_title() {
        return esc_html__('IRM Parallax', 'irm-parallax');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['irm-category'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'irm-parallax'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'background_type',
            [
                'label' => esc_html__( 'Background Type', 'irm-parallax' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Image', 'irm-parallax' ),
                'label_off' => esc_html__( 'Video', 'irm-parallax' ),
                'return_value' => 'image',
                'default' => 'image',
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__('Image', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
                'condition' => [
                    'background_type' => 'image',
                ],
            ]
        );

        $repeater->add_control(
            'video',
            [
                'label' => esc_html__('Video', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                // 'default' => ['url' => 'https://www.youtube.com/watch?v=7e90gBu4pas'],

                'condition' => [
                    // not image
                    'background_type!' => 'image',
                ],
                // allow only mp4, webm, ogg
                'media_type' => 'video',
                'library' => 'uploaded',

            ]
        );

        $repeater->add_control(
            'thumbnail',
            [
                'label' => esc_html__('Thumbnail', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
            ]
        );

        $repeater->add_control(
            'subtitle',
            [
                'label' => esc_html__('Subtitle', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Subtitle', 'irm-parallax'),
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Title', 'irm-parallax'),
            ]
        );

        $repeater->add_control(
            'url',
            [
                'label' => esc_html__('URL', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => '#',
            ]
        );

        $this->add_control(
            'items',
            [
                'label' => esc_html__('Parallax Items', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => esc_html__('Style', 'irm-parallax'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Subtitle Style
        $this->add_control(
            'subtitle_color',
            [
                'label' => esc_html__('Subtitle Color', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project__subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'label' => esc_html__('Subtitle Typography', 'irm-parallax'),
                'selector' => '{{WRAPPER}} .project__subtitle',
            ]
        );

        // Title Style
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project__heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title Typography', 'irm-parallax'),
                'selector' => '{{WRAPPER}} .project__heading',
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        echo '<section class="projects">';
        foreach ($settings['items'] as $item) {
            // if no image is set, skip this item
            if (empty($item['image']['url']) && empty($item['video']['url'])) {
                continue;
            }
            $background_type = $item['background_type'];

            $thumbail = $item['thumbnail']['url'];

            $thumbnail = $item['thumbnail']['url'] ?? '';
            
            if (empty($thumbnail) || $thumbnail === \Elementor\Utils::get_placeholder_image_src()) {
                $thumbnail = $item['image']['url'] ?? '';
            }
            
            echo '<div class="project__image"';
            if (!empty($item['image']['url'])) {
                echo ' style="background-image: url(' . esc_url($item['image']['url']) . ');"';
            }
            echo '>';
            
            // Video handling
            if (!empty($item['video']['url'])) {
                echo '<video loop muted playsinline style="object-fit: cover; width: 100%; height: auto; aspect-ratio: 4 / 3; position: absolute; top: 0; left: 0; z-index: -1;">';
                echo '<source src="' . esc_url($item['video']['url']) . '" type="video/mp4">';
                echo '</video>';
            }
            
            echo '<div class="project__image-pin">';
            echo '<a href="' . esc_url($item['url']) . '" class="project__link">';
            echo '<p class="project__subtitle">' . wp_kses($item['subtitle'], ['br' => []]) . '</p>';
            echo '<p class="project__heading">' . wp_kses($item['title'], ['br' => []]). '</p>';
            echo '</a>';
            
            if (!empty($thumbnail)) {
                echo '<div class="project__image-inner" style="background-image: url(' . esc_url($thumbnail) . ');"></div>';
            }
            
            echo '</div>'; // .project__image-pin
            echo '</div>'; // .project__image
        }
        echo '</section>';
    }
}
