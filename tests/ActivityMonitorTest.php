<?php

namespace VaraTech\ActivityMonitor\Tests;

use VaraTech\ActivityMonitor\Models\Activity;
use VaraTech\ActivityMonitor\Facades\ActivityMonitor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityMonitorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_log_an_activity()
    {
        $activity = ActivityMonitor::log('test_action', ['key' => 'value']);

        $this->assertInstanceOf(Activity::class, $activity);
        $this->assertEquals('test_action', $activity->action);
        $this->assertEquals(['key' => 'value'], $activity->getProperty('key'));
    }

    /** @test */
    public function it_can_log_an_activity_with_description()
    {
        $activity = ActivityMonitor::log('test_action', [], null, 'Test description');

        $this->assertEquals('Test description', $activity->description);
    }

    /** @test */
    public function it_can_query_activities_by_action()
    {
        ActivityMonitor::log('action_one');
        ActivityMonitor::log('action_two');
        ActivityMonitor::log('action_one');

        $activities = Activity::byAction('action_one')->get();

        $this->assertCount(2, $activities);
    }

    /** @test */
    public function it_can_get_recent_activities()
    {
        // Create 5 activities
        for ($i = 0; $i < 5; $i++) {
            ActivityMonitor::log("action_{$i}");
        }

        $recent = Activity::recent(3)->get();

        $this->assertCount(3, $recent);
    }

    /** @test */
    public function it_can_get_and_check_properties()
    {
        $activity = ActivityMonitor::log('test', [
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser',
        ]);

        $this->assertEquals('127.0.0.1', $activity->getProperty('ip_address'));
        $this->assertEquals('default', $activity->getProperty('nonexistent', 'default'));
        $this->assertTrue($activity->hasProperty('user_agent'));
        $this->assertFalse($activity->hasProperty('nonexistent'));
    }
} 