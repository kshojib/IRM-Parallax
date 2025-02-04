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
            'image',
            [
                'label' => esc_html__('Image', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => ['url' => \Elementor\Utils::get_placeholder_image_src()],
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
                    '{{WRAPPER}} .project .subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'label' => esc_html__('Subtitle Typography', 'irm-parallax'),
                'selector' => '{{WRAPPER}} .project .subtitle',
            ]
        );

        // Title Style
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'irm-parallax'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .project .heading' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title Typography', 'irm-parallax'),
                'selector' => '{{WRAPPER}} .project .heading',
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        echo '<section class="projects">';
        echo '<div class="projects__list">';
        foreach ($settings['items'] as $item) {
            // if no image is set, skip this item
            if (empty($item['image']['url'])) {
                continue;
            }
            $thumbail = $item['thumbnail']['url'];
            // if no thumbnail is set & not default image, use the image as the thumbnail
            if (empty($thumbail) || $thumbail === \Elementor\Utils::get_placeholder_image_src()) {
                $thumbail = $item['image']['url'];
            }
            echo '<div class="project" style="background-image: url(' . esc_url($item['image']['url']) . ');">';
            echo '<div class="text-wrapper">';
            echo '<a href="' . esc_url($item['url']) . '" class="text">';
            echo '<p class="subtitle">' . esc_html($item['subtitle']) . '</p>';
            echo '<h2 class="heading">';
            echo '<div class="heading-mask">' . $item['title'] . '</div>';
            echo '</h2>';
            echo '</a>';
            echo '</div>';
            echo '<div class="layer-thumbnail">';
            echo '<img src="' . esc_url($thumbail) . '" alt="">';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '</section>';
    }
}
